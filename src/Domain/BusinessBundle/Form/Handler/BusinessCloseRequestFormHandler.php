<?php

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class BusinessCloseRequestFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var Request $request */
    private $request;

    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /** @var TasksManager $tasksManager */
    private $tasksManager;

    /** @var TranslatorInterface $translator */
    protected $translator;

    public function __construct(
        FormInterface $form,
        Request $request,
        BusinessProfileManager $businessProfileManager,
        TasksManager $tasksManager,
        TranslatorInterface $translator
    ) {
        $this->form                   = $form;
        $this->request                = $request;
        $this->businessProfileManager = $businessProfileManager;
        $this->tasksManager           = $tasksManager;
        $this->translator             = $translator;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess();
                return true;
            }
        }

        return false;
    }

    private function onSuccess()
    {
        $businessProfileId = $this->getRequest()->request->get('businessProfileId', false);

        if (!$businessProfileId) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $closureReason = $this->form->get('reason')->getData();
        $this->tasksManager->createCloseProfileConfirmationRequest($businessProfile, $closureReason);

        $session = $this->request->getSession();

        if ($session) {
            $session->getFlashBag()->add(
                self::MESSAGE_BUSINESS_PROFILE_FLASH_GROUP,
                $this->translator->trans(self::MESSAGE_BUSINESS_PROFILE_CLOSED)
            );
        }
    }

    /**
     * @return Request
     */
    private function getRequest() : Request
    {
        return $this->request;
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }
}
