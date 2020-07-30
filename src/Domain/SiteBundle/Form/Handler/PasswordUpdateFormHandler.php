<?php

namespace Domain\SiteBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PasswordUpdateFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class PasswordUpdateFormHandler extends BaseFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $requestStack;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var  UserInterface */
    protected $currentUser;

    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        UserManagerInterface $userManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->form           = $form;
        $this->requestStack   = $requestStack;
        $this->userManager    = $userManager;
        $this->currentUser    = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return bool
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
     * @return void
     */
    protected function onSuccess()
    {
        $this->currentUser->setPlainPassword($this->getNewPassword());
        $this->userManager->updateUser($this->currentUser);
    }

    /**
     * @return string
     */
    private function getNewPassword() : string
    {
        return $this->form->get('newPassword')->getData();
    }
}
