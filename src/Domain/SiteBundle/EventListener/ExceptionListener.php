<?php

namespace Domain\SiteBundle\EventListener;

use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Mailer\Mailer;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer    = $mailer;
    }

    public function onKernelException(GetResponseForExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getException();

        if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() >= 400) {
            $date = new \DateTime();

            $this->mailer->sendErrorNotification([
                'date'      => $date->format(DatesUtil::DATETIME_FORMAT),
                'url'       => $exceptionEvent->getRequest()->getUri(),
                'errorCode' => $exception->getStatusCode(),
            ]);
        }
    }
}
