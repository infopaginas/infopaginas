<?php

namespace Domain\PageBundle\Form\Handler;

use Domain\ReportBundle\Manager\FeedbackReportManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class FeedbackFormHandler
 * @package Domain\PageBundle\Form\Handler
 */
class FeedbackFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    protected $translationDomain = 'DomainSiteBundle';

    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var Translator */
    protected $translator;

    /** @var FeedbackReportManager */
    protected $feedbackReportManager;

    /** @var User|null */
    protected $user;

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param Translator $translator
     * @param FeedbackReportManager $feedbackReportManager
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        Translator $translator,
        FeedbackReportManager $feedbackReportManager
    ) {
        $this->form           = $form;
        $this->request        = $request;
        $this->translator     = $translator;
        $this->feedbackReportManager = $feedbackReportManager;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->request->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess();

                return true;
            }
        }

        return false;
    }

    protected function onSuccess()
    {
        $data = $this->form->getData();
        $data['locale'] = LocaleHelper::getLocale($this->request->getLocale());
        $data['userId'] = $this->getUserId();

        $this->feedbackReportManager->handleFeedback($data);
    }

    /**
     * @return int|null
     */
    protected function getUserId()
    {
        if ($this->user) {
            $userId = $this->user->getId();
        } else {
            $userId = FeedbackReportManager::MONGO_DB_DEFAULT_USER_ID;
        }

        return $userId;
    }

    /**
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
