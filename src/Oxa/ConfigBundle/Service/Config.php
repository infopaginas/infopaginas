<?php

namespace Oxa\ConfigBundle\Service;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;
use Domain\SearchBundle\Util\CacheUtil;
use Oxa\ConfigBundle\Entity\Config as SystemConfig;
use Oxa\ConfigBundle\Model\ConfigInterface;

class Config
{
    /**
     * @var EntityManager
     */
    private $em;
    private $cache;

    protected $config;

    /**
     * @param EntityManager $em
     * @param CacheProvider $cache
     */
    public function __construct(EntityManager $em, CacheProvider $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    protected function buildConfig()
    {
        $settings = $this->em->getRepository(SystemConfig::class)->findAll();

        foreach ($settings as $setting) {
            $this->config[$setting->getKey()] = $setting;
        }

        $this->cache->save(CacheUtil::ID_CONFIGS, $this->config);
    }

    /**
     * @param string $key
     *
     * @return SystemConfig|null
     */
    public function getSetting($key): ?SystemConfig
    {
        if (!$this->config) {
            $cachedConfigs = $this->cache->fetch(CacheUtil::ID_CONFIGS);
            if ($cachedConfigs) {
                $this->config = $cachedConfigs;
            } else {
                $this->buildConfig();
            }
        }

        return $this->config[$key] ?? null;
    }

    public function getValue($key)
    {
        /** @var SystemConfig $setting */
        if ($setting = $this->getSetting($key)) {
            return $setting->getValue();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getInstantEmail()
    {
        $email = $this->getValue(ConfigInterface::FEEDBACK_EMAIL_ADDRESS);
        $title = $this->getValue(ConfigInterface::FEEDBACK_EMAIL_SUBJECT);

        return sprintf('mailto:%s?subject=%s', $email, $title);
    }
}
