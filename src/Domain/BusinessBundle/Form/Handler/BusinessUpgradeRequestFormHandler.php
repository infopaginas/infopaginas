<?php

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Form\Type\BusinessUpgradeRequestType;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BusinessUpgradeRequestFormHandler
 *
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessUpgradeRequestFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var Request */
    private $request;

    /** @var Mailer */
    private $mailer;

    /** @var User */
    private $currentUser;

    /**
     * BusinessUpgradeRequestFormHandler constructor.
     *
     * @param FormInterface         $form
     * @param Request               $request
     * @param Mailer                $mailer
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        Mailer $mailer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->form        = $form;
        $this->request     = $request;
        $this->mailer      = $mailer;
        $this->currentUser = $tokenStorage->getToken()->getUser();
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

    private function onSuccess()
    {
        $data = $this->form->getData();
        $data['time'] = BusinessUpgradeRequestType::TIME_CHOICES[$data['time']];
        $this->mailer->sendUpdateProfileRequestMessage($this->currentUser, $data);
    }
}
