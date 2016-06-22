<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 21.06.16
 * Time: 11:38
 */

namespace Oxa\Sonata\UserBundle\Handler;

use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationHandler
 * @package Oxa\Sonata\UserBundle\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    const SUCCESS_LOGIN_MESSAGE = 'Successfully logged in. Please wait...';

    /** @var RouterInterface */
    protected $router;

    /** @var SecurityContext */
    protected $security;

    /** @var  UserManagerInterface $userManager */
    protected $userManager;

    /** @var  ContainerInterface */
    protected $service_container;

    /**
     * AuthenticationHandler constructor.
     * @param RouterInterface $router
     * @param SecurityContext $security
     * @param $userManager
     * @param $service_container
     */
    public function __construct(RouterInterface $router, SecurityContext $security, $userManager, $service_container)
    {
        $this->router = $router;
        $this->security = $security;
        $this->userManager = $userManager;
        $this->service_container = $service_container;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new JsonResponse([
            'success' => true,
            'message' => self::SUCCESS_LOGIN_MESSAGE,
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
}
