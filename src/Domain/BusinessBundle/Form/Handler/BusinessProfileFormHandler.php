<?php

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use FOS\UserBundle\Entity\User;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BusinessProfileFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessProfileFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var RequestStack  */
    protected $requestStack;

    /** @var array */
    protected $requestParams;

    /** @var EntityManager */
    protected $em;

    /** @var BusinessProfileManager */
    protected $businessProfileManager;

    /** @var BusinessProfile */
    protected $businessProfileNew;

    /** @var BusinessProfile|null */
    protected $businessProfileOld;

    /** @var TasksManager */
    protected $tasksManager;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var UsersManager $userManager */
    protected $userManager;

    /** @var Translator $translator */
    protected $translator;

    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        ContainerInterface $container
    ) {
        $this->form               = $form;
        $this->requestStack       = $requestStack;
        $this->container          = $container;

        $this->businessProfileManager = $this->container->get('domain_business.manager.business_profile');
        $this->tasksManager       = $this->container->get('domain_business.manager.tasks');
        $this->userManager        = $this->container->get('oxa.manager.users');
        $this->validator          = $this->container->get('validator');
        $this->translator         = $this->container->get('translator');
        $this->em                 = $this->container->get('doctrine.orm.entity_manager');

        $tokenStorage             = $this->container->get('security.token_storage');
        $this->currentUser        = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        $businessProfileId   = $this->requestStack->getCurrentRequest()->get('businessProfileId', false);

        $this->requestParams = $this->requestStack->getCurrentRequest()->request->all()[$this->form->getName()];

        if ($businessProfileId !== false) {
            $this->businessProfileOld = $this->businessProfileManager->find($businessProfileId);
        } else {
            $this->businessProfileOld = null;
        }

        if ($this->businessProfileOld && !$this->businessProfileOld->getIsEditableByUser()) {
            throw new \Exception('You cannot edit this business');
        }

        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            $this->businessProfileNew = $this->form->getData();
            $this->businessProfileNew->setLocale(LocaleHelper::DEFAULT_LOCALE);

            if ($this->businessProfileOld && !$this->businessProfileOld->isEnableNotUniquePhone()) {
                $this->validateNewPhones();
            }

            $serviceAreasType = $this->businessProfileNew->getServiceAreasType();

            if ($serviceAreasType === BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE) {
                $this->setupAreasLocalitiesNeighborhoods();
            }

            $this->handleMediaUpdate();
            $this->handleCategoriesUpdate();

            $this->checkTranslationBlock($this->requestParams);

            $this->checkCategoryCount($this->requestParams, $businessProfileId);

            if ($this->form->isValid()) {
                //create new user entry for not-logged users
                $this->handleBusinessOwner();
                $this->handleTranslationBlock();
                $this->handleSeoBlockUpdate();

                $this->onSuccess();
                return true;
            }
        }

        return false;
    }

    private function validateNewPhones()
    {
        $businessProfilePhoneManager = $this->container->get('domain_business.manager.business_profile_phone');

        $newPhones = $this->businessProfileNew->getPhones()->toArray();
        $oldPhones = $this->businessProfileOld->getPhones()->toArray();

        foreach ($newPhones as $i => $phone) {
            if (!$businessProfilePhoneManager->isNewPhoneValid($phone, $oldPhones)) {
                $error = new FormError($this->translator->trans(
                    'business_profile_phone.not_unique_phone',
                    [],
                    'validators'
                ));
                $this->form->get('phones')->get($i)->get('phone')->addError($error);
            }
        }
    }

    private function onSuccess()
    {
        if (!$this->businessProfileOld) {
            if ($this->currentUser instanceof User) {
                $this->businessProfileNew->setUser($this->currentUser);
            }

            $this->businessProfileManager->saveProfile($this->businessProfileNew);
            $message = self::MESSAGE_BUSINESS_PROFILE_CREATED;

            $this->tasksManager->createNewProfileConfirmationRequest($this->businessProfileNew);
        } else {
            //create 'Update Business Profile' Task for Admin / CM
            $this->tasksManager->createUpdateProfileConfirmationRequest(
                $this->businessProfileNew,
                $this->businessProfileOld
            );

            $message = self::MESSAGE_BUSINESS_PROFILE_UPDATED;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($session) {
            $session->getFlashBag()->add(
                self::MESSAGE_BUSINESS_PROFILE_FLASH_GROUP,
                $this->translator->trans($message)
            );
        }
    }

    private function handleMediaUpdate()
    {
        foreach (BusinessProfile::getTaskMediaManyToOneRelations() as $mediaItem) {
            if (!empty($this->requestParams[$mediaItem])) {
                if ($mediaItem == BusinessProfile::BUSINESS_PROFILE_RELATION_VIDEO) {
                    $repository = $this->em->getRepository(VideoMedia::class);
                } else {
                    $repository = $this->em->getRepository(Media::class);
                }

                $entity = $repository->find($this->requestParams[$mediaItem]['id']);

                $entityNew = clone $entity;

                foreach ($this->requestParams[$mediaItem] as $key => $property) {
                    if (!in_array($key, $this->getSkippedProperties())) {
                        $entityNew->{'set' . ucfirst($key)}($property);
                    }
                }

                $this->businessProfileNew->{'set' . ucfirst($mediaItem)}($entityNew);
            }
        }

        foreach (BusinessProfile::getTaskMediaOneToManyRelations() as $mediaItems) {
            if ($mediaItems == BusinessProfile::BUSINESS_PROFILE_RELATION_IMAGES and
                !empty($this->requestParams[$mediaItems])
            ) {
                $params = $this->requestParams[$mediaItems];

                if ($this->businessProfileOld) {
                    $galleries = $this->businessProfileOld->getImages();

                    foreach ($galleries as $key => $gallery) {
                        if (!empty($params[$key])) {
                            /* @var BusinessGallery gallery */
                            $galleryNew = clone $gallery;

                            $media = $this->em->getRepository(Media::class)->find($params[$key]['media']);

                            $galleryNew->setMedia($media);
                            $galleryNew->setDescription($params[$key]['description']);

                            $this->businessProfileNew->addImage($galleryNew);
                            unset($params[$key]);
                        }
                    }
                }

                foreach ($params as $item) {
                    $galleryNew = new BusinessGallery();

                    $media = $this->em->getRepository(Media::class)->find($item['media']);

                    $galleryNew->setMedia($media);
                    $galleryNew->setDescription($item['description']);

                    $this->businessProfileNew->addImage($galleryNew);
                }
            }
        }

        return $this->businessProfileNew;
    }

    private function handleCategoriesUpdate()
    {
        if (!empty($this->requestParams['categoryIds'])) {
            foreach ($this->requestParams['categoryIds'] as $categoryId) {
                $category = $this->em->getRepository(Category::class)->find((int)$categoryId);

                if ($category) {
                    $this->businessProfileNew->addCategory($category);
                }
            }
        }
    }

    private function handleBusinessOwner()
    {
        if (!empty($this->requestParams['firstname']) and
            !empty($this->requestParams['lastname']) and
            !empty($this->requestParams['email'])
        ) {
            $user = $this->userManager->createMerchantForBusinessProfile(
                $this->requestParams['firstname'],
                $this->requestParams['lastname'],
                $this->requestParams['email']
            );

            $this->businessProfileNew->setUser($user);
        }

        return $this->businessProfileNew;
    }

    private function handleTranslationBlock()
    {
        $fields = BusinessProfile::getTranslatableFields();

        foreach ($fields as $field) {
            LocaleHelper::handleTranslations($this->businessProfileNew, $field, $this->requestParams);
        }

        return $this->businessProfileNew;
    }

    private function handleSeoBlockUpdate()
    {
        return LocaleHelper::handleSeoBlockUpdate($this->businessProfileNew, $this->container);
    }

    /**
     * @param array $post
     */
    private function checkTranslationBlock($post)
    {
        $fields = BusinessProfile::getTranslatableFields();

        //check fields length
        foreach ($fields as $field) {
            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $this->checkFieldLocaleLength($post, $field, $locale);
            }
        }
    }

    /**
     * @param string $field
     *
     * @return int
     */
    private function getFieldMaxLength($field)
    {
        switch ($field) {
            case BusinessProfile::BUSINESS_PROFILE_FIELD_NAME:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME_LENGTH;
                break;
            case BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH;
                break;
            case BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH;
                break;
            case BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS_LENGTH;
                break;
            case BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH;
                break;
            default:
                $maxLength = 0;
                break;
        }

        return $maxLength;
    }

    /**
     * @param array $post
     * @param string $field
     * @param string $locale
     *
     * @return bool
     */
    private function checkFieldLocaleLength($post, $field, $locale)
    {
        $maxLength = $this->getFieldMaxLength($field);
        $fieldName = $field . LocaleHelper::getLangPostfix($locale);

        if (!empty($post[$fieldName]) and mb_strlen($post[$fieldName]) > $maxLength) {
            $formError = new FormError(
                $this->translator->trans('business_profile.max_length', ['{{ limit }}' => $maxLength])
            );

            $this->form->get($fieldName)->addError($formError);

            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    private function getSkippedProperties()
    {
        return [
            'id',
        ];
    }

    private function setupAreasLocalitiesNeighborhoods()
    {
        $areas         = $this->businessProfileNew->getAreas();
        $localities    = $this->businessProfileNew->getLocalities();
        $neighborhoods = $this->businessProfileNew->getNeighborhoods();

        if ($this->businessProfileOld !== null) {
            $oldCatalogLocalityId = $this->businessProfileOld->getCatalogLocality()->getId();
            $isPaid = ($this->businessProfileOld->getSubscriptionPlanCode() > SubscriptionPlanInterface::CODE_FREE);
        } else {
            $oldCatalogLocalityId = null;
            $isPaid = false;
        }

        if ($oldCatalogLocalityId !== $this->businessProfileNew->getCatalogLocality()->getId()) {
            $newLocality      = $this->businessProfileNew->getCatalogLocality();
            $newArea          = $newLocality->getArea();
            $newNeighborhoods = $newLocality->getNeighborhoods();

            if ($isPaid) {
                $this->copyAreasLocalitiesNeighborhoodsFromOldToNew();

                if (!$areas->contains($newArea)) {
                    $areas->add($newArea);
                }

                if (!$localities->contains($newLocality)) {
                    $localities->add($newLocality);
                }

                foreach ($newNeighborhoods as $newNeighborhood) {
                    if (!$neighborhoods->contains($newNeighborhood)) {
                        $neighborhoods->add($newNeighborhood);
                    }
                }
            } else {
                $areas->add($newArea);
                $localities->add($newLocality);

                foreach ($newNeighborhoods as $newNeighborhood) {
                    $neighborhoods->add($newNeighborhood);
                }
            }
        } else {
            $this->copyAreasLocalitiesNeighborhoodsFromOldToNew();
        }
    }

    private function copyAreasLocalitiesNeighborhoodsFromOldToNew()
    {
        foreach ($this->businessProfileOld->getAreas() as $area) {
            $this->businessProfileNew->addArea($area);
        }

        foreach ($this->businessProfileOld->getLocalities() as $locality) {
            $this->businessProfileNew->addLocality($locality);
        }

        foreach ($this->businessProfileOld->getNeighborhoods() as $neighborhood) {
            $this->businessProfileNew->addNeighborhood($neighborhood);
        }
    }


    /**
     * @param array $post
     * @param integer $businessProfileId
     */
    private function checkCategoryCount($post, $businessProfileId)
    {
        if (isset($post['categoryIds'])) {
            $maxCategoriesNumber = BusinessProfile::BUSINESS_PROFILE_FREE_MAX_CATEGORIES_COUNT;
            $categoriesCount = count($post['categoryIds']);

            if ($businessProfileId) {
                if ($this->businessProfileOld->getSubscriptionPlanCode() > SubscriptionPlanInterface::CODE_FREE) {
                    $maxCategoriesNumber = false;
                }
            }

            if ($categoriesCount > $maxCategoriesNumber && $maxCategoriesNumber) {
                $this->form->get('categoryIds')->addError(new FormError($this->translator->trans('business_profile.category.max')));
            }
        }
    }
}
