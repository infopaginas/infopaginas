<?php

namespace Domain\EmergencyBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\EmergencyBundle\Entity\EmergencyDraftBusiness;
use Domain\EmergencyBundle\Entity\EmergencyService;
use Domain\SearchBundle\Model\DataType\EmergencySearchDTO;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

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

        $orderCategoryByAlphabet = $this->getEmergencyCatalogOrderByAlphabet();
        $catalogItems = $this->getCatalogWithContent($orderCategoryByAlphabet);

        foreach ($catalogItems as $catalogItem) {
            $area     = $catalogItem->getArea();
            $category = $catalogItem->getCategory();

            $data[$area->getId()]['area'] = $catalogItem->getArea();
            $data[$area->getId()]['categories'][$category->getId()]['category']  = $category;
            $data[$area->getId()]['categories'][$category->getId()]['updatedAt'] = $catalogItem->getContentUpdatedAt();
        }

        return $data;
    }

    /**
     * @param bool $orderCategoryByAlphabet
     *
     * @return EmergencyCatalogItem[]
     */
    public function getCatalogWithContent($orderCategoryByAlphabet)
    {
        return $this->em->getRepository(EmergencyCatalogItem::class)
            ->getCatalogItemWithContent($orderCategoryByAlphabet);
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
     * @return EmergencyDraftBusiness
     */
    public function getEmergencyBusinessDraft() : EmergencyDraftBusiness
    {
        return new EmergencyDraftBusiness();
    }

    /**
     * @param EmergencyDraftBusiness
     */
    public function createBusinessDraft($draft)
    {
        $this->em->persist($draft);
        $this->em->flush();
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

    /**
     * @return bool
     */
    public function getEmergencyCatalogOrderByAlphabet()
    {
        return (bool)$this->config->getSetting(ConfigInterface::EMERGENCY_CATALOG_ORDER_BY_ALPHABET)->getValue();
    }

    /**
     * @return IterableResult
     */
    public function getUpdatedLocalitiesIterator()
    {
        $localities = $this->em->getRepository(EmergencyBusiness::class)->getActiveBusinessIterator();

        return $localities;
    }

    /**
     * @param EmergencyArea $area
     * @param EmergencyCategory $category
     *
     * @return array
     */
    public function getCatalogItemCharacterFilters($area, $category)
    {
        $filtersData = $this->em->getRepository(EmergencyCatalogItem::class)
            ->getCatalogItemFilterCharacters($area, $category);

        if ($filtersData) {
            $filters = json_decode(current($filtersData));
        } else {
            $filters = [];
        }

        return $filters;
    }

    /**
     * @return EmergencyService[]
     */
    public function getCatalogItemServiceFilters()
    {
        $filters = $this->em->getRepository(EmergencyService::class)->getServiceFilters();

        return $filters;
    }

    /**
     * @return mixed
     */
    public function setUpdatedAllEmergencyBusinesses()
    {
        $data = $this->em->getRepository(EmergencyBusiness::class)->setUpdatedAllEmergencyBusinesses();

        return $data;
    }

    /**
     * @param EmergencyBusiness $business
     *
     * @return array
     */
    public function buildEmergencyBusinessElasticData($business)
    {
        $name = trim(AdminHelper::convertAccentedString($business->getName()));

        $serviceIds = [];

        foreach ($business->getServices() as $service) {
            $serviceIds[] = $service->getId();
        }

        $data = [
            'id'           => $business->getId(),
            'title'        => SearchDataUtil::sanitizeElasticSearchQueryString($name),
            'area_id'      => $business->getArea()->getId(),
            'category_id'  => $business->getCategory()->getId(),
            'first_symbol' => $business->getFirstSymbol(),
            'service_ids'  => $serviceIds,
        ];

        return $data;
    }

    /**
     * @return array
     */
    public static function getEmergencyBusinessElasticSearchIndexParams(): array
    {
        return [
            'title' => [
                'type'  => 'text',
            ],
            'first_symbol' => [
                'type'  => 'keyword',
            ],
            'area_id' => [
                'type' => 'integer'
            ],
            'category_id' => [
                'type' => 'integer'
            ],
            'location' => [
                'type' => 'geo_point',
            ],
        ];
    }

    /**
     * @param array $response
     *
     * @return array
     */
    public function getEmergencyBusinessesFromElasticResponse($response)
    {
        $data  = [];
        $total = 0;

        if (!empty($response['hits']['total'])) {
            $total = $response['hits']['total'];
        }

        if (!empty($response['hits']['hits'])) {
            $result = $response['hits']['hits'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['_id'];
            }

            $data = $this->getAvailableBusinessesByIds($dataIds);
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param array $ids
     *
     * @return EmergencyBusiness[]
     */
    public function getAvailableBusinessesByIds($ids)
    {
        $businesses = $this->em->getRepository(EmergencyBusiness::class)->getAvailableBusinessesByIds($ids);
        $data = [];

        foreach ($ids as $id) {
            $item = SiteHelper::searchEntityByIdsInArray($businesses, $id);

            if ($item) {
                $data[] = $item;
            }
        }

        return $data;
    }
}
