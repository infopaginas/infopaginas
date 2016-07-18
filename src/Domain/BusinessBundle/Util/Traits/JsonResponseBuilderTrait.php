<?php

namespace Domain\BusinessBundle\Util\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;

trait JsonResponseBuilderTrait
{
    /**
     * @param string $message
     * @return JsonResponse
     */
    function getSuccessResponse(string $message) : JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $this->getTranslator()->trans($message),
        ]);
    }

    /**
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    function getFailureResponse(string $message = '', array $errors = [], int $status = 200) : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $this->getTranslator()->trans($message),
            'errors'  => $errors,
        ], $status);
    }

    /**
     * @return TranslatorInterface
     */
    function getTranslator() : TranslatorInterface
    {
        return $this->get('translator');
    }
}
