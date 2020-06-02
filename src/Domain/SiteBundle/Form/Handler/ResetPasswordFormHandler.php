<?php

namespace Domain\SiteBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResetPasswordFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class ResetPasswordFormHandler extends BaseFormHandler
{
    protected $translationDomain = 'DomainSiteBundle';

    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $requestStack;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var Translator */
    protected $translator;
    
    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        UserManagerInterface $userManager,
        Translator $translator
    ) {
        $this->form           = $form;
        $this->requestStack   = $requestStack;
        $this->userManager    = $userManager;
        $this->translator     = $translator;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        $token = $this->requestStack->getCurrentRequest()->request->get('token', null);

        if ($token === null) {
            throw new \Exception(
                $this->translator->trans('user.reset_password.token.empty')
            );
        }

        $usersManager = $this->getUsersManager();

        $user = $usersManager->findUserByConfirmationToken($token);

        if ($user === null) {
            throw new NotFoundHttpException(
                $this->translator->trans('user.reset_password.token.invalid')
            );
        }

        if ($this->requestStack->getCurrentRequest()->getMethod() == 'POST') {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $password = $this->form->get('plainPassword')->getData();

                $this->onSuccess($user, $password);

                return true;
            }
        }

        return false;
    }

    /**
     * @param UserInterface $user
     * @param string $password
     */
    protected function onSuccess(UserInterface $user, string $password)
    {
        $user->setPlainPassword($password);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);

        $this->getUsersManager()->updateUser($user);
    }

    /**
     * @return UserManagerInterface
     */
    private function getUsersManager() : UserManagerInterface
    {
        return $this->userManager;
    }
}
