<?php

namespace Oxa\ConfigBundle\Service;

use Doctrine\ORM\EntityManager;

class Config
{
    /**
     * @var EntityManager
     */
    private $em;

    protected $config;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function buildConfig()
    {
        $settings = $this->em->getRepository('OxaConfigBundle:Config')->findAll();

        foreach ($settings as $setting) {
            $this->config[$setting->getKey()] = $setting;
        }
    }

    public function getSetting($key)
    {
        if (!$this->config) {
            $this->buildConfig();
        }

        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    public function getValue($key)
    {
        if ($setting = $this->getSetting($key)) {
            return $setting->getValue();
        }
        return '';
    }
}
