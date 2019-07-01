<?php

namespace Domain\SiteBundle\EventListener;

use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Mailer\Mailer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    private   $container;
    protected $mailer;

    public function __construct(ContainerInterface $container, Mailer $mailer)
    {
        $this->container = $container;
        $this->mailer    = $mailer;
    }

    public function onKernelException(GetResponseForExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getException();

        if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() >= 400) {
            $date = new \DateTime();

            $this->mailer->sendErrorNotification([
                'date'       => $date->format(DatesUtil::DATETIME_FORMAT),
                'url'        => $this->container->get('request')->getRequestUri(),
                'errorCode' => $exception->getStatusCode(),
            ]);
        }
    }
}
