<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Model\BusinessUndefinedCategoryModel;
use Domain\BusinessBundle\Model\CategoryUpdateModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170106120206 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $businessList = BusinessUndefinedCategoryModel::getBusinesses();
        $categoryMapping = CategoryUpdateModel::getCategoryMapping();

        $batchSize = 20;
        $i = 0;

        foreach ($businessList as $key => $item) {
            $categoryName = trim($item['category']);

            if (!empty($categoryMapping[$categoryName])) {
                $categoryCode = $categoryMapping[$categoryName];

                $category = $this->getCategoryByCode($categoryCode);

                if ($category) {
                    $business = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
                        ->findOneBy(['uid' => $key]);

                    if ($business) {
                        $undefinedCategory = $business->getCategories();

                        foreach ($undefinedCategory as $undefined) {
                            $business->removeCategory($undefined);
                        }

                        $business->addCategory($category);

                        $parent1 = $category->getParent();

                        if ($parent1) {
                            $business->addCategory($parent1);

                            $parent2 = $parent1->getParent();

                            if ($parent2) {
                                $business->addCategory($parent2);
                            }
                        }
                    }
                }
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;
        }
    }

    protected function getCategoryByCode($code)
    {
        $category = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(
            [
                'isActive' => true,
                'code' => $code,
            ]
        );

        return $category;
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
