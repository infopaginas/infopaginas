<?php

namespace Domain\EmergencyBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\LockHandler;

class EmergencyCatalogItemContentCommand extends ContainerAwareCommand
{
    const EMERGENCY_CATALOG_LOCK = 'EMERGENCY_CATALOG.lock';

    /* @var EntityManager $em */
    protected $em;

    /* @var Config $config */
    protected $config;

    protected function configure()
    {
        $this
            ->setName('domain:business:catalog-item-update:emergency')
            ->setDescription('Update emergency catalog items content persistence')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockHandler = new LockHandler(self::EMERGENCY_CATALOG_LOCK);

        if (!$lockHandler->lock()) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->config = $this->getContainer()->get('oxa_config');

            $this->updateCatalogItem();
        }
    }

    protected function updateCatalogItem()
    {
        if ($this->getEmergencyFeatureEnabled()) {
            $catalogAreas = $this->em->getRepository(EmergencyArea::class)->getAllAreasIterator();

            foreach ($catalogAreas as $areaRow) {
                /* @var $catalogArea EmergencyArea */
                $catalogArea = current($areaRow);

                $categories = $this->em->getRepository(EmergencyCategory::class)->getAllCategoriesIterator();

                $countAreaContent = 0;

                foreach ($categories as $categoryRow) {
                    /* @var $category EmergencyCategory */
                    $category = current($categoryRow);

                    $catalogItem = $this->em->getRepository(EmergencyCatalogItem::class)->findOneBy(
                        [
                            'area'     => $catalogArea->getId(),
                            'category' => $category->getId(),
                        ]
                    );

                    if (!$catalogItem) {
                        $catalogItem = $this->createCatalogItem($catalogArea, $category);
                    }

                    $countCategoryContent = $this->getCountCatalogItemContent($catalogArea, $category);

                    $catalogItem->setHasContent((bool)$countCategoryContent);

                    if ($countCategoryContent) {
                        $lastUpdated = $this->getCatalogItemContentLastUpdated($catalogArea, $category);

                        if ($lastUpdated > $catalogItem->getContentUpdatedAt()) {
                            $catalogItem->setContentUpdatedAt($lastUpdated);
                        }

                        $charactersData = $this->getCatalogItemFilterCharacters($catalogArea, $category);

                        if ($charactersData) {
                            $characters = [];

                            foreach ($charactersData as $row) {
                                $characters[] = current($row);
                            }

                            $catalogItem->setFilters(json_encode($characters));
                        }
                    }

                    $countAreaContent += $countCategoryContent;
                }

                $catalogItem = $this->em->getRepository(EmergencyCatalogItem::class)->findOneBy(
                    [
                        'area'     => $catalogArea->getId(),
                        'category' => null,
                    ]
                );

                if (!$catalogItem) {
                    $catalogItem = $this->createCatalogItem($catalogArea);
                }

                $catalogItem->setHasContent((bool)$countAreaContent);

                $this->em->flush();
                $this->em->clear();
            }
        }
    }

    /**
     * @param EmergencyArea $area
     * @param EmergencyCategory $category
     *
     * @return int
     */
    protected function getCountCatalogItemContent($area, $category)
    {
        return $this->em->getRepository(EmergencyBusiness::class)->countCatalogItemContent($area, $category);
    }

    /**
     * @param EmergencyArea $area
     * @param EmergencyCategory $category
     *
     * @return \Datetime|null
     */
    protected function getCatalogItemContentLastUpdated($area, $category)
    {
        return $this->em->getRepository(EmergencyBusiness::class)->getCatalogItemContentLastUpdated($area, $category);
    }

    /**
     * @param EmergencyArea $area
     * @param EmergencyCategory $category
     *
     * @return array
     */
    protected function getCatalogItemFilterCharacters($area, $category)
    {
        return $this->em->getRepository(EmergencyBusiness::class)->getCatalogItemFilterCharacters($area, $category);
    }

    /**
     * @param EmergencyArea $area
     * @param EmergencyCategory|null $category
     *
     * @return EmergencyCatalogItem
     */
    protected function createCatalogItem($area, $category = null)
    {
        $catalogItem = new EmergencyCatalogItem();

        $catalogItem->setArea($area);
        $catalogItem->setCategory($category);

        $this->em->persist($catalogItem);

        return $catalogItem;
    }

    /**
     * @return bool
     */
    protected function getEmergencyFeatureEnabled()
    {
        return (bool)$this->config->getSetting(ConfigInterface::EMERGENCY_SITUATION_ON)->getValue();
    }
}
