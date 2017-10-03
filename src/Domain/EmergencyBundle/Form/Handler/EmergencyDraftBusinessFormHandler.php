<?php

namespace Domain\EmergencyBundle\Form\Handler;

use Domain\BusinessBundle\Form\Handler\BusinessFormHandlerInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\EmergencyBundle\Entity\EmergencyDraftBusiness;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EmergencyDraftBusinessFormHandler
 * @package Domain\EmergencyBundle\Form\Handler
 */
class EmergencyDraftBusinessFormHandler extends BaseFormHandler
{
    /** @var Request $request */
    private $request;

    /** @var EmergencyManager $emergencyManager */
    private $emergencyManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(
        FormInterface $form,
        Request $request,
        EmergencyManager $emergencyManager,
        TranslatorInterface $translator
    ) {
        $this->form             = $form;
        $this->request          = $request;
        $this->emergencyManager = $emergencyManager;
        $this->translator       = $translator;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->request->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->request);

            /** @var EmergencyDraftBusiness $draft */
            $draft = $this->form->getData();

            if ($this->form->isValid()) {
                $this->onSuccess($draft);
                return true;
            }
        }

        return false;
    }

    /**
     * @param EmergencyDraftBusiness $draft
     */
    private function onSuccess(EmergencyDraftBusiness $draft)
    {
        $this->emergencyManager->createBusinessDraft($draft);

        $session = $this->request->getSession();

        if ($session) {
            $session->getFlashBag()->add(
                BusinessFormHandlerInterface::MESSAGE_BUSINESS_PROFILE_FLASH_GROUP,
                $this->translator->trans(BusinessFormHandlerInterface::MESSAGE_EMERGENCY_BUSINESS_CREATED)
            );
        }
    }
}
