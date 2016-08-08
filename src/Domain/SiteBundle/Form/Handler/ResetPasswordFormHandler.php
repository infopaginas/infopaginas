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
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResetPasswordFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class ResetPasswordFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    const ERROR_EMPTY_TOKEN = 'Error: Empty token.';

    const ERROR_USER_NOT_FOUND_BY_TOKEN = 'You currently do not have valid link for update your password';

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
            throw new NotFoundHttpException(self::ERROR_USER_NOT_FOUND_BY_TOKEN);
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
     * @return UserManagerInterface
     */
    private function getUsersManager() : UserManagerInterface
    {
        return $this->userManager;
    }
}
