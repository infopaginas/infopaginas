<?php

namespace Domain\BusinessBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\CatalogItem;
use Domain\BusinessBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CatalogItemContentCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('domain:business:catalog-item-update')
            ->setDescription('Update catalog items content persistence')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateCatalogItem();
    }

    protected function updateCatalogItem()
    {
        $catalogLocalities = $this->em->getRepository('DomainBusinessBundle:Locality')
            ->getAllLocalitiesIterator();

        foreach ($catalogLocalities as $localityRow) {
            /* @var $catalogLocality \Domain\BusinessBundle\Entity\Locality */
            $catalogLocality = current($localityRow);

            $categories = $this->em->getRepository('DomainBusinessBundle:Category')
                ->getAllCategoriesIterator();

            $countLocalityContent = 0;

            foreach ($categories as $categoryRow) {
                /* @var $category \Domain\BusinessBundle\Entity\Category */
                $category = current($categoryRow);

                $catalogItem = $this->em->getRepository('DomainBusinessBundle:CatalogItem')->findOneBy(
                    [
                        'locality' => $catalogLocality->getId(),
                        'category' => $category->getId(),
                    ]
                );

                if (!$catalogItem) {
                    $catalogItem = new CatalogItem();
                    $catalogItem->setLocality($catalogLocality);
                    $catalogItem->setCategory($category);

                    $this->em->persist($catalogItem);
                }

                if ($catalogLocality->getIsActive() and $category->getIsActive()) {
                    $countCategoryContent = $this->getCountCatalogItemContent($catalogLocality, $category);

                    $catalogItem->setHasContent((bool)$countCategoryContent);

                    $countLocalityContent += $countCategoryContent;
                } else {
                    $catalogItem->setHasContent(false);
                }
            }

            $catalogItem = $this->em->getRepository('DomainBusinessBundle:CatalogItem')->findOneBy(
                [
                    'locality' => $catalogLocality->getId(),
                    'category' => null,
                ]
            );

            if (!$catalogItem) {
                $catalogItem = new CatalogItem();
                $catalogItem->setLocality($catalogLocality);
                $catalogItem->setCategory(null);

                $this->em->persist($catalogItem);
            }

            if ($catalogLocality->getIsActive()) {
                $catalogItem->setHasContent((bool)$countLocalityContent);
            } else {
                $catalogItem->setHasContent(false);
            }

            $this->em->flush();
            $this->em->clear();
        }
    }

    protected function getCountCatalogItemContent($catalogLocality, $category)
    {
        $countCatalogItemContent = $this->em->getRepository('DomainBusinessBundle:CatalogItem')
            ->getCountCatalogItemContent($catalogLocality, $category);

        return $countCatalogItemContent;
    }
}
