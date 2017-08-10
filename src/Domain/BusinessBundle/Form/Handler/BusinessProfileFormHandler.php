<?php

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
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
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BusinessProfileFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessProfileFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var Request  */
    protected $request;

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

    /**
     * FreeBusinessProfileFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ContainerInterface $container
    ) {
        $this->form               = $form;
        $this->request            = $request;
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
     */
    public function process()
    {
        $businessProfileId   = $this->request->get('businessProfileId', false);

        $this->requestParams = $this->request->request->all()[$this->form->getName()];

        if ($businessProfileId !== false) {
            $this->businessProfileOld = $this->businessProfileManager->find($businessProfileId);
        } else {
            $this->businessProfileOld = null;
        }

        if ($this->request->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->request);

            $this->businessProfileNew = $this->form->getData();
            $this->businessProfileNew->setLocale(LocaleHelper::DEFAULT_LOCALE);

            $this->handleMediaUpdate();
            $this->handleCategoriesUpdate();

            $this->checkTranslationBlock($this->requestParams);
            $this->checkCollectionWorkingHoursBlock($this->businessProfileNew->getCollectionWorkingHours());

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

        $session = $this->request->getSession();

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
        //check name not blank
        $this->checkTranslationBlockNameBlank($post);

        $fields = BusinessProfile::getTranslatableFields();

        //check fields length
        foreach ($fields as $field) {
            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $this->checkFieldLocaleLength($post, $field, $locale);
            }
        }
    }

    /**
     * @param ArrayCollection $workingHours
     */
    private function checkCollectionWorkingHoursBlock($workingHours)
    {
        if (!$workingHours->isEmpty()) {
            if (!DayOfWeekModel::validateWorkingHoursTime($workingHours)) {
                $formError = new FormError($this->translator->trans('form.collectionWorkingHours.duration'));

                $this->form->get('collectionWorkingHoursError')->addError($formError);
            }

            if (!DayOfWeekModel::validateWorkingHoursOverlap($workingHours)) {
                $formError = new FormError($this->translator->trans('form.collectionWorkingHours.overlap'));

                $this->form->get('collectionWorkingHoursError')->addError($formError);
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
            case BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS:
                $maxLength = BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH;
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
     * @param array $post
     *
     * @return bool
     */
    private function checkTranslationBlockNameBlank($post)
    {
        foreach (LocaleHelper::getLocaleList() as $locale => $name) {
            $fieldName = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . LocaleHelper::getLangPostfix($locale);

            if (!empty($post[$fieldName]) and trim($post[$fieldName])) {
                return true;
            }
        }

        $formError = new FormError($this->translator->trans('business_profile.names_blank'));

        foreach (LocaleHelper::getLocaleList() as $locale => $name) {
            $fieldName = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . LocaleHelper::getLangPostfix($locale);

            $this->form->get($fieldName)->addError($formError);
        }

        return false;
    }

    private function cloneBusinessGallery(BusinessProfile $businessProfile)
    {
        $data = [];

        // track only required property that should be added to task view
        foreach ($businessProfile->getImages() as $gallery) {
            $data[$gallery->getId()] = [
                'type'        => $gallery->getType(),
                'description' => $gallery->getDescription(),
            ];
        }

        return $data;
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
}
