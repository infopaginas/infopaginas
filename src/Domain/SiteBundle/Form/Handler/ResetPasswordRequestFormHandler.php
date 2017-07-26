<?php

namespace Domain\SiteBundle\Form\Handler;

use Domain\SiteBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class ResetPasswordFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class ResetPasswordRequestFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    const ERROR_USER_NOT_FOUND = 'user.reset_password_request.email.not_found';

    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var  TokenGeneratorInterface */
    protected $tokenGenerator;

    /** @var  Mailer */
    protected $mailer;

    /** @var Translator */
    protected $translator;

    /**
     * ResetPasswordRequestFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param Mailer $mailer
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        Mailer $mailer,
        Translator $translator
    ) {
        $this->form           = $form;
        $this->request        = $request;
        $this->userManager    = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer         = $mailer;
        $this->translator     = $translator;
    }

    /**
     * @return bool
     * @throws \Exception
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

    /**
     * @throws \Exception
     */
    protected function onSuccess()
    {
        $email = $this->form->get('email')->getData();

        $usersManager = $this->getUsersManager();

        $user = $usersManager->findUserByUsernameOrEmail($email);

        if ($user === null) {
            throw new \Exception($this->translator->trans(self::ERROR_USER_NOT_FOUND, ['{-email-}' => $email]));
        }

        if ($user->getConfirmationToken() === null) {
            $user->setConfirmationToken($this->getTokenGenerator()->generateToken());
        }

        $this->getMailer()->sendResetPasswordEmailMessage($user);

        $user->setPasswordRequestedAt(new \DateTime());

        $usersManager->updateUser($user);
    }

    /**
     * @return Mailer
     */
    private function getMailer() : Mailer
    {
        return $this->mailer;
    }

    /**
     * @return TokenGeneratorInterface
     */
    private function getTokenGenerator() : TokenGeneratorInterface
    {
        return $this->tokenGenerator;
    }

    /**
     * @return UserManagerInterface
     */
    private function getUsersManager() : UserManagerInterface
    {
        return $this->userManager;
    }
}
