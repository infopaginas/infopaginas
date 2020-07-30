<?php

namespace Domain\EmergencyBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\EmergencyBundle\Entity\EmergencyDraftBusiness;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EmergencyDraftBusinessFormHandler
 * @package Domain\EmergencyBundle\Form\Handler
 */
class EmergencyDraftBusinessFormHandler extends BaseFormHandler
{
    /** @var Request $requestStack */
    private $requestStack;

    /** @var EmergencyManager $emergencyManager */
    private $emergencyManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        EmergencyManager $emergencyManager,
        TranslatorInterface $translator
    ) {
        $this->form             = $form;
        $this->requestStack     = $requestStack;
        $this->emergencyManager = $emergencyManager;
        $this->translator       = $translator;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            /** @var EmergencyDraftBusiness $draft */
            $draft = $this->form->getData();

            if ($this->form->isValid()) {
                $this->onSuccess($draft);
                return true;
            }
        }

        return false;
    }

    private function onSuccess(EmergencyDraftBusiness $draft): void
    {
        $this->emergencyManager->createBusinessDraft($draft);
    }
}
