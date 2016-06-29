<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 28.06.16
 * Time: 20:29
 */

namespace Domain\SiteBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PasswordUpdateFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class PasswordUpdateFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var  UserInterface */
    protected $currentUser;

    /**
     * PasswordUpdateFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        UserManagerInterface $userManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->form           = $form;
        $this->request        = $request;
        $this->userManager    = $userManager;
        $this->currentUser    = $tokenStorage->getToken()->getUser();
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

    /**
     * @param null $form
     * @return array
     */
    public function getErrors($form = null) : array
    {
        $errors = [];

        if ($form === null) {
            $form = $this->form;
        }

        if ($form->count()) {
            /** @var FormInterface $child */
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrors($child);
                }
            }
        } else {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
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
