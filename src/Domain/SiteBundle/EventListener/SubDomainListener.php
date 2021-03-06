<?php

namespace Domain\SiteBundle\EventListener;

use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class SubDomainListener implements EventSubscriberInterface
{
    protected $container;
    private $router;

    public function __construct(ContainerInterface $container, Router $router, Environment $twig)
    {
        $this->container = $container;
        $this->router = $router;
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $locale = $this->getCurrentLocale($request);

        $request->setLocale($locale);
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }

    private function getCurrentLocale($request)
    {
        $localeAdmin = $this->container->getParameter('locale_admin');

        if (strpos(trim($request->getPathInfo(), '/'), $localeAdmin['url_part']) === 0) {
            // is admin

            return $localeAdmin['locale'];
        }

        $languages = $this->container->getParameter('locale_data');

        $host = $request->getHttpHost();

        $baseHost = '';

        foreach ($languages as $alias => $language) {
            $domainKey = $this->getDomainKey($language['domain']);

            if ($domainKey and strpos($host, $domainKey) === 0) {
                $locale = $alias;

                $baseHost = str_replace($domainKey, '', $host);

                $languages[$alias]['active'] = true;
            } else {
                $languages[$alias]['active'] = false;
            }
        }

        if (!$baseHost) {
            $baseHost = $host;

            $locale = LocaleHelper::LOCALE_ES;
            $languages[$locale]['active'] = true;
        }

        foreach ($languages as $alias => $language) {
            $domainKey = $this->getDomainKey($language['domain']);

            // work around for symfony as it request ignores HTTPS
            $schemeAndHost = str_replace(
                $request->getScheme(),
                'https',
                $request->getSchemeAndHttpHost()
            );

            $langSchemeAndHttpHost = str_replace($host, $domainKey . $baseHost, $schemeAndHost);

            $languages[$alias]['url'] = $langSchemeAndHttpHost . $request->getRequestUri();
        }

        $this->twig->addGlobal('languages', $languages);

        return $locale;
    }

    private function getDomainKey($alias)
    {
        if ($alias) {
            $domainKey = $alias . '.';
        } else {
            $domainKey = '';
        }

        return $domainKey;
    }
}
