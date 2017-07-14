<?php

namespace Oxa\ConfigBundle\Service;

use Doctrine\ORM\EntityManager;
use Oxa\ConfigBundle\Entity\Config as SystemConfig;

class Config
{
    /**
     * @var EntityManager
     */
    private $em;

    protected $config;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function buildConfig()
    {
        $settings = $this->em->getRepository(SystemConfig::class)->findAll();

        foreach ($settings as $setting) {
            $this->config[$setting->getKey()] = $setting;
        }
    }

    /**
     * @param string $key
     *
     * @return Config|null
     */
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
