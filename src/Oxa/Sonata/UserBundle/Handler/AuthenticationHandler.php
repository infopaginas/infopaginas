<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 21.06.16
 * Time: 11:38
 */

namespace Oxa\Sonata\UserBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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

    const REDIRECTS = [
        'suggest-edits' => 'domain_business_suggest_edits_index',
    ];

    /** @var TranslatorInterface $translator */
    private $translator;
    private $router;
    private $authorizationChecker;

    /**
     * AuthenticationHandler constructor.
     *
     * @param TranslatorInterface           $translator
     * @param RouterInterface               $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->translator           = $translator;
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
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

        if ($this->getAuthorizationChecker()->isGranted($adminRoles)) {
            $redirect = $this->getRouter()->generate('sonata_admin_dashboard');
        } else {
            $redirectData = json_decode($request->request->get('_redirect'), true);

            if (isset($redirectData['type']) && array_key_exists($redirectData['type'], self::REDIRECTS)) {
                $routeName = self::REDIRECTS[$redirectData['type']];
                $redirect = $this->getRouter()->generate($routeName, $redirectData['params'] ?? []);
            }
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

    /**
     * @return RouterInterface
     */
    private function getRouter() : RouterInterface
    {
        return $this->router;
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    private function getAuthorizationChecker() : AuthorizationCheckerInterface
    {
        return $this->authorizationChecker;
    }
}
