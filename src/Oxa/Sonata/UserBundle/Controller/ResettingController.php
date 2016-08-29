<?php

namespace Oxa\Sonata\UserBundle\Controller;

use Sonata\UserBundle\Controller\ResettingFOSUser1Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ResettingController
 *
 * @package Oxa\Sonata\UserBundle\Controller
 */
class ResettingController extends ResettingFOSUser1Controller
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        return $this->container->get('templating')
            ->renderResponse('OxaSonataUserBundle:Admin/Security/Resetting:request.html.twig', [
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            ]);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')
            ->renderResponse('OxaSonataUserBundle:Admin/Security/Resetting:checkEmail.html.twig', [
                'admin_pool' => $this->container->get('sonata.admin.pool'),
                'email'      => $email,
            ]);
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction()
    {
        $username = $this->container->get('request')->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')
                ->renderResponse('OxaSonataUserBundle:Admin/Security/Resetting:request.html.twig', [
                    'admin_pool' => $this->container->get('sonata.admin.pool'),
                    'invalid_username' => $username,
                ]);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')
                ->renderResponse('OxaSonataUserBundle:Admin/Security/Resetting:passwordAlreadyRequested.html.twig', [
                    'admin_pool' => $this->container->get('sonata.admin.pool'),
                ]);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }
}
