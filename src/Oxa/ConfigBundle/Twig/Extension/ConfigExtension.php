<?php

namespace Oxa\ConfigBundle\Twig\Extension;

use Oxa\ConfigBundle\Service\Config;

/**
 * Class ConfigExtension
 * @package Oxa\ConfigBundle\Twig\Extension
 */
class ConfigExtension extends \Twig_Extension
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getFunctions()
    {
        return array('config' => new \Twig_Function_Method($this, 'getSetting', [
            'needs_environment'=> true,
            'is_safe' => [
                'all'
            ]
        ]));
    }

    public function getSetting($env, $key)
    {
        if ($setting = $this->config->getSetting($key)) {
            $value = $setting->getValue();

            if ($setting->getFormat() == 'text') {
                $value = twig_escape_filter($env, $value);
            }

            return $value;
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'config';
    }
}
