<?php

namespace Domain\PageBundle\Form\Handler;

use Domain\ReportBundle\Manager\FeedbackReportManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FeedbackFormHandler
 * @package Domain\PageBundle\Form\Handler
 */
class FeedbackFormHandler extends BaseFormHandler
{
    protected $translationDomain = 'DomainSiteBundle';

    /** @var FormInterface  */
    protected $form;

    /** @var RequestStack  */
    protected $requestStack;

    /** @var Translator */
    protected $translator;

    /** @var FeedbackReportManager */
    protected $feedbackReportManager;

    /** @var User|null */
    protected $user;
    
    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        Translator $translator,
        FeedbackReportManager $feedbackReportManager
    ) {
        $this->form           = $form;
        $this->requestStack   = $requestStack;
        $this->translator     = $translator;
        $this->feedbackReportManager = $feedbackReportManager;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $this->onSuccess();

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(): void
    {
        $data = $this->form->getData();
        $data['locale'] = LocaleHelper::getLocale($this->requestStack->getCurrentRequest()->getLocale());
        $data['userId'] = $this->getUserId();

        $this->feedbackReportManager->handleFeedback($data);
    }

    protected function getUserId(): ?int
    {
        if ($this->user) {
            $userId = $this->user->getId();
        } else {
            $userId = FeedbackReportManager::MONGO_DB_DEFAULT_USER_ID;
        }

        return $userId;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }
}
