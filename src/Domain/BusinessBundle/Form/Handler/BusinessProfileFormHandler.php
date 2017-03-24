<?php

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use FOS\UserBundle\Entity\User;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
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
class BusinessProfileFormHandler extends BaseFormHandler
{
    const MESSAGE_BUSINESS_PROFILE_CREATED = 'business_profile.message.created';
    const MESSAGE_BUSINESS_PROFILE_UPDATED = 'business_profile.message.updated';

    const MESSAGE_BUSINESS_PROFILE_FLASH_GROUP = 'success';

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

            $this->handleMediaUpdate();
            $this->handleCategoriesUpdate();

            $this->checkTranslationBlock($this->requestParams);
            $this->checkCollectionWorkingHoursBlock($this->businessProfileNew->getCollectionWorkingHours());

            if ($this->form->isValid()) {
                //create new user entry for not-logged users
                $this->handleBusinessOwner();
                $this->handleTranslationBlock();
                $this->handleSeoBlockUpdate();
                $this->setSearchParams();

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

            $this->businessProfileManager->saveProfile(
                $this->businessProfileNew,
                strtolower(BusinessProfile::TRANSLATION_LANG_ES)
            );
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
                    $repository = $this->em->getRepository('OxaVideoBundle:VideoMedia');
                } else {
                    $repository = $this->em->getRepository('OxaSonataMediaBundle:Media');
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

                            $media = $this->em->getRepository('OxaSonataMediaBundle:Media')
                                ->find($params[$key]['media']);

                            $galleryNew->setMedia($media);
                            $galleryNew->setDescription($params[$key]['description']);

                            $this->businessProfileNew->addImage($galleryNew);
                            unset($params[$key]);
                        }
                    }
                }

                foreach ($params as $item) {
                    $galleryNew = new BusinessGallery();

                    $media = $this->em->getRepository('OxaSonataMediaBundle:Media')->find($item['media']);

                    $galleryNew->setMedia($media);
                    $galleryNew->setDescription($item['description']);

                    $this->businessProfileNew->addImage($galleryNew);
                }
            }
        }

        return $this->businessProfileNew;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getCategoriesIds($data)
    {
        $ids = [];

        if (!empty($data['categories'])) {
            $ids[] = $data['categories'];

            if (!empty($data['categories2'])) {
                $ids = array_merge($ids, $data['categories2']);

                if (!empty($data['categories3'])) {
                    $ids = array_merge($ids, $data['categories3']);
                }
            }
        }

        return $ids;
    }

    private function handleCategoriesUpdate()
    {
        $newCategoryIds  = $this->getCategoriesIds($this->requestParams);

        if (!$newCategoryIds) {
            $this->form->get('categories')->addError(new FormError('business_profile.category.min_count'));
        } else {
            $categories = $this->businessProfileManager->getCategoriesByIds($newCategoryIds);

            foreach ($categories as $category) {
                $this->businessProfileNew->addCategory($category);
            }
        }

        return $this->businessProfileNew;
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
            $this->handleTranslationSet($field, $this->requestParams);
        }

        return $this->businessProfileNew;
    }

    private function handleTranslationSet($property, $data)
    {
        $propertyEn = $property . BusinessProfile::TRANSLATION_LANG_EN;
        $propertyEs = $property . BusinessProfile::TRANSLATION_LANG_ES;

        $dataEn = false;
        $dataEs = false;

        if (!empty($data[$propertyEn])) {
            $dataEn = trim($data[$propertyEn]);
        }

        if (!empty($data[$propertyEs])) {
            $dataEs = trim($data[$propertyEs]);
        }

        if (property_exists($this->businessProfileNew, $property)) {
            if ($dataEs) {
                if ($this->businessProfileOld and $this->businessProfileOld->{'get' . $property}() and $dataEn) {
                    $this->businessProfileNew->{'set' . $property}($dataEn);
                } else {
                    $this->businessProfileNew->{'set' . $property}($dataEs);
                }

                if (property_exists($this->businessProfileNew, $propertyEs)) {
                    $this->businessProfileNew->{'set' . $propertyEs}($dataEs);
                }

                $this->addBusinessTranslation($property, $dataEs, BusinessProfile::TRANSLATION_LANG_ES);
            } elseif ($dataEn) {
                if (!$this->businessProfileNew->{'get' . $property}()) {
                    $this->businessProfileNew->{'set' . $property}($dataEn);
                }
            }

            if ($dataEn) {
                $this->addBusinessTranslation($property, $dataEn, BusinessProfile::TRANSLATION_LANG_EN);

                if (property_exists($this->businessProfileNew, $propertyEn)) {
                    $this->businessProfileNew->{'set' . $propertyEn}($dataEn);
                }
            }
        }

        return $this->businessProfileNew;
    }

    private function handleSeoBlockUpdate()
    {
        $seoTitleEn = BusinessProfileUtil::seoTitleBuilder(
            $this->businessProfileNew,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoTitleEs = BusinessProfileUtil::seoTitleBuilder(
            $this->businessProfileNew,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $seoDescriptionEn = BusinessProfileUtil::seoDescriptionBuilder(
            $this->businessProfileNew,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoDescriptionEs = BusinessProfileUtil::seoDescriptionBuilder(
            $this->businessProfileNew,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $this->handleTranslationSet(
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            [
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_EN => $seoTitleEn,
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_ES => $seoTitleEs,
            ]
        );

        $seoDescKeyEn = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_EN;
        $seoDescKeyEs = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_ES;

        $this->handleTranslationSet(
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
            [
                $seoDescKeyEn => $seoDescriptionEn,
                $seoDescKeyEs => $seoDescriptionEs,
            ]
        );

        return $this->businessProfileNew;
    }

    private function checkTranslationBlock($post)
    {
        //check name not blank
        $this->checkTranslationBlockNameBlank($post);

        $fields = BusinessProfile::getTranslatableFields();

        //check fields length
        foreach ($fields as $field) {
            $this->checkFieldLocaleLength($post, $field, BusinessProfile::TRANSLATION_LANG_EN);
            $this->checkFieldLocaleLength($post, $field, BusinessProfile::TRANSLATION_LANG_ES);
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

    private function checkFieldLocaleLength($post, $field, $locale)
    {
        $maxLength = $this->getFieldMaxLength($field);
        $fieldName = $field . $locale;

        if (!empty($post[$fieldName]) and mb_strlen($post[$fieldName]) > $maxLength) {
            $formError = new FormError(
                $this->translator->trans('business_profile.max_length', ['{{ limit }}' => $maxLength])
            );

            $this->form->get($fieldName)->addError($formError);

            return false;
        }

        return true;
    }

    private function checkTranslationBlockNameBlank($post)
    {
        $fieldNameEn = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . BusinessProfile::TRANSLATION_LANG_EN;
        $fieldNameES = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . BusinessProfile::TRANSLATION_LANG_ES;

        if ((!empty($post[$fieldNameEn]) and
            trim($post[$fieldNameEn])) or
            (!empty($post[$fieldNameES]) and
            trim($post[$fieldNameES]))
        ) {
            return true;
        }

        $formError = new FormError($this->translator->trans('business_profile.names_blank'));

        $this->form->get($fieldNameEn)->addError($formError);
        $this->form->get($fieldNameES)->addError($formError);

        return false;
    }

    private function setSearchParams()
    {
        $data = [
            BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,
            BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,
        ];

        foreach ($data as $field) {
            $valueEn = $this->businessProfileNew->{'get' . $field . BusinessProfile::TRANSLATION_LANG_EN}();
            $valueEs = $this->businessProfileNew->{'get' . $field . BusinessProfile::TRANSLATION_LANG_ES}();

            if ($valueEn and !$valueEs) {
                $this->businessProfileNew->{'set' . $field . BusinessProfile::TRANSLATION_LANG_ES}($valueEn);
            } elseif (!$valueEn and $valueEs) {
                $this->businessProfileNew->{'set' . $field . BusinessProfile::TRANSLATION_LANG_EN}($valueEs);
            }
        }

        return $this->businessProfileNew;
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

    private function addBusinessTranslation($property, $data, $locale)
    {
        if ($this->businessProfileOld) {
            $translation = $this->businessProfileOld->getTranslationItem(
                $property,
                mb_strtolower($locale)
            );

            if ($translation) {
                $translationNew = clone $translation;

                $translationNew->setContent($data);
            } else {
                $translationNew = new BusinessProfileTranslation(
                    mb_strtolower($locale),
                    $property,
                    $data
                );
            }

            $this->businessProfileNew->addTranslation($translationNew);
        } else {
            $translationNew = new BusinessProfileTranslation(
                mb_strtolower($locale),
                $property,
                $data
            );

            $this->businessProfileNew->addTranslation($translationNew);
        }

        return $this->businessProfileNew;
    }

    private function getSkippedProperties()
    {
        return [
            'id',
        ];
    }
}
