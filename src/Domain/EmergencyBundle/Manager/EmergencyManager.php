<?php

namespace Domain\EmergencyBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;

class EmergencyManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param EntityManager $entityManager
     * @param Config        $config
     */
    public function __construct(EntityManager $entityManager, Config $config)
    {
        $this->em = $entityManager;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getCatalogItemsWithContent()
    {
        $data = [];
        $catalogItems = $this->getCatalogWithContent();

        foreach ($catalogItems as $catalogItem) {
            $data[$catalogItem->getArea()->getId()]['area']         = $catalogItem->getArea();
            $data[$catalogItem->getArea()->getId()]['categories'][] = $catalogItem->getCategory();
        }

        return $data;
    }

    /**
     * @return EmergencyCatalogItem[]
     */
    public function getCatalogWithContent()
    {
        return $this->em->getRepository(EmergencyCatalogItem::class)->getCatalogItemWithContent();
    }

    /**
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     * @param int               $page
     *
     * @return EmergencyBusiness[]
     */
    public function getBusinessByAreaAndCategory($area, $category, $page = 1)
    {
        $limit = $this->getSystemItemPerPage();

        return $this->em->getRepository(EmergencyBusiness::class)->getBusinessByAreaAndCategory($area, $category, $limit, $page);
    }

    /**
     * @param string $slug
     *
     * @return EmergencyCategory
     */
    public function getCategoryBySlug($slug)
    {
        return $this->em->getRepository(EmergencyCategory::class)->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * @param string $slug
     * @return EmergencyArea
     */
    public function getAreaBySlug($slug)
    {
        return $this->em->getRepository(EmergencyArea::class)->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * @return bool
     */
    public function getEmergencyFeatureEnabled()
    {
        return (bool)$this->config->getSetting(ConfigInterface::EMERGENCY_SITUATION_ON)->getValue();
    }

    /**
     * @return int
     */
    public function getSystemItemPerPage()
    {
        return (int)$this->config->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
    }
}
