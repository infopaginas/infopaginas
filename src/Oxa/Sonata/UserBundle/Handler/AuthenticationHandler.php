<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 21.06.16
 * Time: 11:38
 */

namespace Oxa\Sonata\UserBundle\Handler;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationHandler
 * @package Oxa\Sonata\UserBundle\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    const SUCCESS_LOGIN_MESSAGE = 'Successfully logged in. Please wait...';

    /** @var  Translator $translator */
    protected $translator;

    /**
     * AuthenticationHandler constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
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
            'message' => $this->getTranslator()->trans(self::SUCCESS_LOGIN_MESSAGE),
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
     * @return Translator
     */
    private function getTranslator() : Translator
    {
        return $this->translator;
    }
}
