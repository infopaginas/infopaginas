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
            $businessProfile = $this->manager->find($businessProfileId);

            if ($locale !== BusinessProfile::DEFAULT_LOCALE) {
                $businessProfile->setLocale($locale);
            }

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
        if (!$businessProfile->getId()) {
            $this->getTasksManager()->createNewProfileConfirmationRequest($businessProfile);
            $this->getBusinessProfilesManager()->saveProfile($businessProfile);
        } else {
            $businessProfile = $this->getBusinessProfilesManager()->checkBusinessProfileVideo($businessProfile);
            //create 'Update Business Profile' Task for Admin / CM
            $this->getTasksManager()->createUpdateProfileConfirmationRequest($businessProfile);
        }
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
