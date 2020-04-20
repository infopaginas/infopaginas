<?php

namespace Oxa\ConfigBundle\Twig\Extension;

use Oxa\ConfigBundle\Service\Config;
use Twig\TwigFunction;

/**
 * Class ConfigExtension
 * @package Oxa\ConfigBundle\Twig\Extension
 */
class ConfigExtension extends \Twig_Extension
{
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'config',
                [
                    $this,
                    'getSetting',
                ],
                [
                    'needs_environment' => true,
                    'is_safe'           => [
                        'all',
                    ],
                ]
            ),
        ];
    }

    public function getSetting($env, $key)
    {
        $setting = $this->config->getSetting($key);

        if ($setting) {
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
