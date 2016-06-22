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
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class AuthenticationHandler
 * @package Oxa\Sonata\UserBundle\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    const SUCCESS_LOGIN_MESSAGE = 'Successfully logged in. Please wait...';

    /** @var  DataCollectorTranslator $translator */
    protected $translator;

    /**
     * AuthenticationHandler constructor.
     * @param DataCollectorTranslator $translator
     */
    public function __construct(DataCollectorTranslator $translator)
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

    private function getTranslator() : DataCollectorTranslator
    {
        return $this->translator;
    }
}
