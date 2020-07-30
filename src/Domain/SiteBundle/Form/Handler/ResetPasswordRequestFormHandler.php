<?php

namespace Domain\SiteBundle\Form\Handler;

use Domain\SiteBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ResetPasswordFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class ResetPasswordRequestFormHandler extends BaseFormHandler
{
    private const ERROR_USER_NOT_FOUND = 'user.reset_password_request.email.not_found';

    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $requestStack;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var  TokenGeneratorInterface */
    protected $tokenGenerator;

    /** @var  Mailer */
    protected $mailer;

    /** @var Translator */
    protected $translator;
    
    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        Mailer $mailer,
        Translator $translator
    ) {
        $this->form           = $form;
        $this->requestStack   = $requestStack;
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
        if ($this->requestStack->getCurrentRequest()->getMethod() == 'POST') {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

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
