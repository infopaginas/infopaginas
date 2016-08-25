<?php

namespace Domain\SiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SubDomainListener
{
    protected $container;
    private $router;

    public function __construct(ContainerInterface $container, Router $router, \Twig_Environment $twig)
    {
        $this->container = $container;
        $this->router = $router;
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request   = $event->getRequest();

        $locale = $this->getCurrentLocale($request);

        $request->setLocale($locale);
    }

    private function getCurrentLocale($request)
    {
        $locateAdmin = $this->container->getParameter('locate_admin');

        if (strpos(trim($request->getPathInfo(), '/'), $locateAdmin['url_part']) === 0) {
            // is admin

            return $locateAdmin['locale'];
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

            $locale = 'es';
            $languages[$locale]['active'] = true;
        }

        foreach ($languages as $alias => $language) {
            $domainKey = $this->getDomainKey($language['domain']);

            $langSchemeAndHttpHost = str_replace($host, $domainKey . $baseHost, $request->getSchemeAndHttpHost());

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
