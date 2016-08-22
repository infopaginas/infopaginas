<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 19:48
 */

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        ValidatorInterface $validator
    ) {
        $this->form               = $form;
        $this->request            = $request;
        $this->manager            = $manager;
        $this->tasksManager       = $tasksManager;
        $this->validator          = $validator;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $businessProfileId = $this->request->get('businessProfileId', false);

        $locale = $this->request->get('locale', BusinessProfile::DEFAULT_LOCALE);

        if ($businessProfileId !== false) {

            /** @var BusinessProfile $actualBusinessProfile */
            $actualBusinessProfile = $this->manager->find($businessProfileId);

            $businessProfile = $this->manager->cloneProfile($actualBusinessProfile);

            if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
                $businessProfile->setLocale($locale);
            }

            $businessProfile->setActualBusinessProfile($actualBusinessProfile);

            $this->form->setData($businessProfile);
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

            if ($this->form->isValid()) {
                $this->onSuccess($businessProfile);
                return true;
            }
        }

        return false;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    private function onSuccess(BusinessProfile $businessProfile)
    {
        if ($businessProfile->getActualBusinessProfile() === null) {
            $this->getTasksManager()->createNewProfileConfirmationRequest($businessProfile);
        } else {
            $businessProfile = $this->getBusinessProfilesManager()->checkBusinessProfileVideo($businessProfile);

            //Pessimistic block strategy used - user can't update his business profile before Admin response
            $this->getBusinessProfilesManager()->lock($businessProfile->getActualBusinessProfile());

            //create 'Update Business Profile' Task for Admin / CM
            $this->getTasksManager()->createUpdateProfileConfirmationRequest($businessProfile);
        }

        $this->getBusinessProfilesManager()->saveProfile($businessProfile);
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
}
