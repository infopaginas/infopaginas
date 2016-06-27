<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 27.06.16
 * Time: 11:32
 */

namespace Domain\SiteBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResetPasswordFormHandler
{
    const ERROR_EMPTY_TOKEN = 'Error: Empty token.';

    const ERROR_USER_NOT_FOUND_BY_TOKEN = 'The user with "confirmation token" does not exist for value "%s"';

    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * ResetPasswordFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param UserManagerInterface $userManager
     */
    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager)
    {
        $this->form           = $form;
        $this->request        = $request;
        $this->userManager    = $userManager;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        $token = $this->request->request->get('token', null);

        if ($token === null) {
            throw new \Exception(self::ERROR_EMPTY_TOKEN);
        }

        $usersManager = $this->getUsersManager();

        $user = $usersManager->findUserByConfirmationToken($token);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf(self::ERROR_USER_NOT_FOUND_BY_TOKEN, $token));
        }

        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

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
     * @return UserManagerInterface
     */
    private function getUsersManager() : UserManagerInterface
    {
        return $this->userManager;
    }
}
