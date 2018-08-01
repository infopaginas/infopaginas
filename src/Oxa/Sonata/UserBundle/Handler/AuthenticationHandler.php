<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 21.06.16
 * Time: 11:38
 */

namespace Oxa\Sonata\UserBundle\Handler;

use Domain\BusinessBundle\Form\Handler\BusinessFormHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AuthenticationHandler
 * @package Oxa\Sonata\UserBundle\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    const SUCCESS_LOGIN_MESSAGE = 'Successfully logged in. Please wait...';

    /** @var  TranslatorInterface $translator */
    protected $translator;

    /**
     * AuthenticationHandler constructor.
     * @param TranslatorInterface $translator
     * @param Router              $router
     * @param SecurityContext     $security
     */
    public function __construct(TranslatorInterface $translator, Router $router, SecurityContext $security)
    {
        $this->translator = $translator;
        $this->router     = $router;
        $this->security   = $security;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $redirect = false;

        $adminRoles = [
            'ROLE_ADMINISTRATOR',
            'ROLE_CONTENT_MANAGER',
            'ROLE_SALES_MANAGER',
        ];

        if ($this->security->isGranted($adminRoles)) {
            $redirect = $this->router->generate('sonata_admin_dashboard');
        }

        return new JsonResponse([
            'success'  => true,
            'message'  => $this->getTranslator()->trans(self::SUCCESS_LOGIN_MESSAGE),
            'redirect' => $redirect,
        ]);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'success' => false,
            'message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator() : TranslatorInterface
    {
        return $this->translator;
    }
}
