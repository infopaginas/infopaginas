<?php

namespace Domain\EmergencyBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\SearchBundle\Model\Manager\SearchManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EmergencyCatalogItemContentCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    /* @var SearchManager $searchManager */
    protected $searchManager;

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
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->searchManager = $this->getContainer()->get('domain_search.manager.search');

        $this->updateCatalogItem();
    }

    protected function updateCatalogItem()
    {
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
}
