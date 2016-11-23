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
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use FOS\UserBundle\Entity\User;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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
        UsersManager $userManager
    ) {
        $this->form               = $form;
        $this->request            = $request;
        $this->manager            = $manager;
        $this->tasksManager       = $tasksManager;
        $this->validator          = $validator;
        $this->currentUser        = $tokenStorage->getToken()->getUser();
        $this->userManager        = $userManager;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $businessProfileId = $this->request->get('businessProfileId', false);

        $locale = $this->request->get('locale', BusinessProfile::DEFAULT_LOCALE);
        $post   = $this->request->request->all()[$this->form->getName()];

        $oldCategories = [];

        if ($businessProfileId !== false) {
            /* @var BusinessProfile $businessProfile */
            $businessProfile = $this->manager->find($businessProfileId);

            if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
                $businessProfile->setLocale($locale);
            }

            $this->form->setData($businessProfile);

            //workaround for category/subcategories update
            $oldCategories = clone $businessProfile->getCategories();
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

            if ($this->form->isValid()) {
                //create new user entry for not-logged users
                if (isset($post['firstname']) && isset($post['lastname'])) {
                    if (!empty($post['firstname']) && !empty($post['lastname']) && !empty($post['email'])) {
                        $user = $this->getUsersManager()
                            ->createMerchantForBusinessProfile($post['firstname'], $post['lastname'], $post['email']);

                        $businessProfile->setUser($user);
                    }
                }

                if ($locale == 'en') {
                    $businessProfile->setNameEn($businessProfile->getName());

                    if (!$businessProfile->getNameEs()) {
                        $businessProfile->setNameEs($businessProfile->getName());
                    }
                } else {
                    $businessProfile->setNameEs($businessProfile->getName());

                    if (!$businessProfile->getNameEn()) {
                        $businessProfile->setNameEn($businessProfile->getName());
                    }
                }

                $this->onSuccess($businessProfile, $oldCategories);
                return true;
            }
        }

        return false;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param Collection      $oldCategories
     */
    private function onSuccess(BusinessProfile $businessProfile, $oldCategories)
    {
        if (!$businessProfile->getId()) {
            $this->getTasksManager()->createNewProfileConfirmationRequest($businessProfile);

            if ($this->currentUser instanceof User) {
                $businessProfile->setUser($this->currentUser);
            }

            $this->getBusinessProfilesManager()->saveProfile($businessProfile);
        } else {
            $businessProfile = $this->getBusinessProfilesManager()->checkBusinessProfileVideo($businessProfile);
            //create 'Update Business Profile' Task for Admin / CM
            $this->getTasksManager()->createUpdateProfileConfirmationRequest($businessProfile, $oldCategories);
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

        if (isset($data['categories']) and $data['categories']) {
            $ids[] = $data['categories'];

            if (isset($data['subcategories']) and $data['subcategories']) {
                $ids = array_merge($ids, $data['subcategories']);
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
}
