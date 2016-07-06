<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 19:48
 */

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfilesManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FreeBusinessProfileFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class FreeBusinessProfileFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var BusinessProfilesManager */
    protected $manager;

    /** @var TasksManager */
    protected $tasksManager;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * FreeBusinessProfileFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param BusinessProfilesManager $manager
     * @param TasksManager $tasksManager
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        BusinessProfilesManager $manager,
        TasksManager $tasksManager,
        ValidatorInterface $validator
    ) {
        $this->form         = $form;
        $this->request      = $request;
        $this->manager      = $manager;
        $this->tasksManager = $tasksManager;
        $this->validator    = $validator;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            $businessProfile = $this->form->getData();

            //$validationGroups = $this->getValidationGroups($businessProfile);
            //$validationErrors = $this->validator->validate($businessProfile, null, $validationGroups);

            /** @var FormError $error */
            /*foreach ($validationErrors as $error) {
                var_dump($error->getMessage());
            }
            die();*/

            if ($this->form->isValid()) {
                $this->onSuccess($businessProfile);
                return true;
            }
        }

        return false;
    }

    /**
     * @param null $form
     * @return array
     */
    public function getErrors($form = null) : array
    {
        $errors = [];

        if ($form === null) {
            $form = $this->form;
        }

        if ($form->count()) {
            /** @var FormInterface $child */
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrors($child);
                }
            }
        } else {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    private function onSuccess(BusinessProfile $businessProfile)
    {
        $this->getBusinessProfilesManager()->saveProfile($businessProfile);
        $this->getTasksManager()->createNewProfileConfirmationRequest($businessProfile);
    }

    private function getValidationGroups(BusinessProfile $formData)
    {
        $groups = ['Default'];

        if ($formData->getServiceAreasType() == 'area') {
            array_push($groups, 'service_area_chosen');
        }

        return $groups;
    }

    /**
     * @return TasksManager
     */
    private function getTasksManager() : TasksManager
    {
        return $this->tasksManager;
    }

    /**
     * @return BusinessProfilesManager
     */
    private function getBusinessProfilesManager() : BusinessProfilesManager
    {
        return $this->manager;
    }
}
