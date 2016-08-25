<?php

namespace Domain\SiteBundle\Utils;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class DynamicDomainSessionStorage extends NativeSessionStorage
{
    public function setOptions(array $options)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];

            $dotPosition = strpos($host, '.');

            if ($dotPosition == 2) {
                // is locale subdomain

                $domain = substr($host, $dotPosition);
            } else {
                $domain = '.' . $host;
            }

            $options['cookie_domain'] = $domain;
        }

        return parent::setOptions($options);
    }
}
