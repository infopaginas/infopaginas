<?php

namespace Domain\SiteBundle\EventListener;

use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Mailer\Mailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    private const INVALID_METHOD_OVERRIDE_MESSAGE = 'Invalid method override';

    protected $mailer;
    protected $logger;

    public function __construct(Mailer $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getException();

        if (
            method_exists($exception, 'getStatusCode') && $exception->getStatusCode() >= 400
            && $exception->getStatusCode() != 404
            && $exception->getStatusCode() != 410
        ) {
            $date = new \DateTime();

            if (strpos($exception->getMessage(), self::INVALID_METHOD_OVERRIDE_MESSAGE) === false) {
                $this->mailer->sendErrorNotification(
                    [
                        'date' => $date->format(DatesUtil::DATETIME_FORMAT),
                        'url' => $exceptionEvent->getRequest()->getUri(),
                        'errorCode' => $exception->getStatusCode(),
                    ]
                );
            }

            $this->logger->error(
                'HttpErrorCode: ' . $exception->getStatusCode() . ' ' . $exceptionEvent->getRequest()->getUri(),
                [
                    'content' => $exceptionEvent->getRequest()->getContent(),
                    'message' => $exception->getMessage(),
                ]
            );
        }
    }
}
