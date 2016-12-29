<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 19:48
 */

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\Common\Collections\Collection;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use FOS\UserBundle\Entity\User;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
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
class BusinessProfileFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    /** @var Request  */
    protected $request;

    /** @var BusinessProfileManager */
    protected $manager;

    /** @var TasksManager */
    protected $tasksManager;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var UsersManager $userManager */
    protected $userManager;

    /** @var Translator $userManager */
    protected $translator;

    /**
     * FreeBusinessProfileFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param BusinessProfileManager $manager
     * @param TasksManager $tasksManager
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        BusinessProfileManager $manager,
        TasksManager $tasksManager,
        ValidatorInterface $validator,
        TokenStorageInterface $tokenStorage,
        UsersManager $userManager,
        ContainerInterface $container
    ) {
        $this->form               = $form;
        $this->request            = $request;
        $this->manager            = $manager;
        $this->tasksManager       = $tasksManager;
        $this->validator          = $validator;
        $this->currentUser        = $tokenStorage->getToken()->getUser();
        $this->userManager        = $userManager;
        $this->container          = $container;
        $this->translator         = $this->container->get('translator');
    }

    /**
     * @return bool
     */
    public function process()
    {
        $businessProfileId = $this->request->get('businessProfileId', false);
        $post   = $this->request->request->all()[$this->form->getName()];

        $oldCategories  = [];
        $oldImages      = [];

        if ($businessProfileId !== false) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile = $this->manager->find($businessProfileId);

            $this->form->setData($businessProfile);

            //workaround for category/subcategories update
            $oldCategories = clone $businessProfile->getCategories();

            //workaround for businessGallery properties update
            $oldImages     = $this->cloneBusinessGallery($businessProfile);
        }

        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            /** @var BusinessProfile $businessProfile */
            $businessProfile = $this->form->getData();

            if (!isset($this->request->request->all()[$this->form->getName()]['video'])) {
                $businessProfile->setVideo(null);

                if ($businessProfile->getIsSetVideo()) {
                    $businessProfile->setIsSetVideo(false);
                }
            } else {
                $businessProfile->setIsSetVideo(true);
            }

            $newCategoryIds  = $this->getCategoriesIds($post);
            $businessProfile = $this->handleCategoriesUpdate($businessProfile, $newCategoryIds);

            if (!$newCategoryIds) {
                $this->form->get('categories')->addError(new FormError('business_profile.category.min_count'));
            }

            $this->checkTranslationBlock($post);

            if ($this->form->isValid()) {
                //create new user entry for not-logged users
                if (isset($post['firstname']) && isset($post['lastname'])) {
                    if (!empty($post['firstname']) && !empty($post['lastname']) && !empty($post['email'])) {
                        $user = $this->getUsersManager()
                            ->createMerchantForBusinessProfile($post['firstname'], $post['lastname'], $post['email']);

                        $businessProfile->setUser($user);
                    }
                }

                $translations = $businessProfile->getTranslations();

                foreach ($translations as $item) {
                    $businessProfile->removeTranslation($item);
                }

                $businessProfile = $this->handleTranslationBlock($businessProfile, $post);
                $businessProfile = $this->handleSeoBlockUpdate($businessProfile);

                $businessProfile = $this->setSearchParams($businessProfile);

                $this->onSuccess($businessProfile, $oldCategories, $oldImages);
                return true;
            }
        }

        return false;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param Collection      $oldCategories
     * @param array           $oldImages
     */
    private function onSuccess(BusinessProfile $businessProfile, $oldCategories, $oldImages)
    {
        if (!$businessProfile->getId()) {
            $businessProfile = $this->getBusinessProfilesManager()->preSaveProfile($businessProfile);
            $this->getTasksManager()->createNewProfileConfirmationRequest($businessProfile);

            if ($this->currentUser instanceof User) {
                $businessProfile->setUser($this->currentUser);
            }

            $this->getBusinessProfilesManager()->saveProfile($businessProfile);
        } else {
            $businessProfile = $this->getBusinessProfilesManager()->preSaveProfile($businessProfile);
            //create 'Update Business Profile' Task for Admin / CM

            $this->getTasksManager()->createUpdateProfileConfirmationRequest(
                $businessProfile,
                $oldCategories,
                $oldImages
            );
        }
    }

    private function getUsersManager() : UsersManager
    {
        return $this->userManager;
    }

    /**
     * @return TasksManager
     */
    private function getTasksManager() : TasksManager
    {
        return $this->tasksManager;
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->manager;
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

    /**
     * @param BusinessProfile $businessProfile
     * @param array           $newCategoryIds
     * @return BusinessProfile
     */
    private function handleCategoriesUpdate($businessProfile, $newCategoryIds)
    {
        if ($newCategoryIds) {
            foreach ($businessProfile->getCategories() as $item) {
                $key = array_search($item->getId(), $newCategoryIds);

                if ($key) {
                    unset($newCategoryIds[$key]);
                } else {
                    $businessProfile->removeCategory($item);
                }
            }

            if ($newCategoryIds) {
                $categories = $this->manager->getCategoriesByIds($newCategoryIds);

                foreach ($categories as $category) {
                    $businessProfile->addCategory($category);
                }
            }
        }

        return $businessProfile;
    }

    private function handleTranslationBlock(BusinessProfile $businessProfile, $post)
    {
        $fields = BusinessProfile::getTranslatableFields();

        foreach ($fields as $field) {
            $businessProfile = $this->handleTranslationSet($businessProfile, $field, $post);
        }

        return $businessProfile;
    }

    private function handleTranslationSet(BusinessProfile $businessProfile, $property, $post)
    {
        $propertyEn = $property . BusinessProfile::TRANSLATION_LANG_EN;
        $propertyEs = $property . BusinessProfile::TRANSLATION_LANG_ES;

        $dataEn = false;
        $dataEs = false;

        if (!empty($post[$propertyEn])) {
            $dataEn = trim($post[$propertyEn]);
        }

        if (!empty($post[$propertyEs])) {
            $dataEs = trim($post[$propertyEs]);
        }

        if (property_exists($businessProfile, $property)) {
            if ($dataEn) {
                $businessProfile->{'set' . $property}($dataEn);

                $translation = new BusinessProfileTranslation(
                    strtolower(BusinessProfile::TRANSLATION_LANG_EN),
                    $property,
                    $dataEn
                );

                $businessProfile->addTranslation($translation);

                if (property_exists($businessProfile, $propertyEn)) {
                    $businessProfile->{'set' . $propertyEn}($dataEn);
                }
            } elseif ($dataEs) {
                $businessProfile->{'set' . $property}($dataEs);
            }

            if ($dataEs) {
                $translation = new BusinessProfileTranslation(
                    strtolower(BusinessProfile::TRANSLATION_LANG_ES),
                    $property,
                    $dataEs
                );

                $businessProfile->addTranslation($translation);

                if (property_exists($businessProfile, $propertyEs)) {
                    $businessProfile->{'set' . $propertyEs}($dataEs);
                }
            }
        }

        return $businessProfile;
    }

    private function handleSeoBlockUpdate(BusinessProfile $businessProfile)
    {
        $seoTitleEn = BusinessProfileUtil::seoTitleBuilder(
            $businessProfile,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoTitleEs = BusinessProfileUtil::seoTitleBuilder(
            $businessProfile,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $seoDescriptionEn = BusinessProfileUtil::seoDescriptionBuilder(
            $businessProfile,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoDescriptionEs = BusinessProfileUtil::seoDescriptionBuilder(
            $businessProfile,
            $this->container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $businessProfile = $this->handleTranslationSet(
            $businessProfile,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            [
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_EN => $seoTitleEn,
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_ES => $seoTitleEs,
            ]
        );

        $businessProfile = $this->handleTranslationSet(
            $businessProfile,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
            [
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_EN => $seoDescriptionEn,
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_ES => $seoDescriptionEs,
            ]
        );

        return $businessProfile;
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

    private function setSearchParams(BusinessProfile $businessProfile)
    {
        $data = [
            BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,
            BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,
        ];

        foreach ($data as $field) {
            $valueEn = $businessProfile->{'get' . $field . BusinessProfile::TRANSLATION_LANG_EN}();
            $valueEs = $businessProfile->{'get' . $field . BusinessProfile::TRANSLATION_LANG_ES}();

            if ($valueEn and !$valueEs) {
                $businessProfile->{'set' . $field . BusinessProfile::TRANSLATION_LANG_ES}($valueEn);
            } elseif (!$valueEn and $valueEs) {
                $businessProfile->{'set' . $field . BusinessProfile::TRANSLATION_LANG_EN}($valueEs);
            }
        }

        return $businessProfile;
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
}
