<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\BusinessBundle\Entity\Locality;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170111140736 extends AbstractMigration implements ContainerAwareInterface
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
        //delete location
        $deleteList = $this->getLocalitiesDeleteList();
        $parentList = $this->getParentList();

        foreach ($deleteList as $item) {
            if ($item['parent']) {
                $parentItem = $parentList[$item['parent']];
            } else {
                $parentItem = false;
            }

            $parent = $this->getParent($parentItem);
            $locality = $this->getLocalityItemByName($item['name']);

            if ($locality && $parent) {
                $businesses = $this->getBusinessIteratorByLocalityId($locality->getId());

                foreach ($businesses as $row) {
                    /* @var $business BusinessProfile */
                    $business = $row[0];

                    if ($business->getCatalogLocality() and
                        $business->getCatalogLocality()->getId() == $locality->getId()
                    ) {
                        $business->setCatalogLocality($parent);
                    }

                    if ($business->getLocalities()->contains($locality)) {
                        if (!$business->getLocalities()->contains($parent)) {
                            $business->addLocality($parent);
                        }

                        $business->removeLocality($locality);
                    }
                }

                $this->em->flush();
                $this->em->remove($locality);

                $this->em->clear();
            }
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function getParentList()
    {
        $parent = [
            57 => [
                'id' => '57',
                'name' => 'Ponce',
            ],
        ];

        return $parent;
    }

    protected function getLocalitiesDeleteList()
    {
        $delete = [
            113 => [
                'id' => '113',
                'name' => 'Coto Laurel',
                'parent' => '57',
            ],
        ];

        return $delete;
    }

    protected function getParent($data)
    {
        if ($data) {
            $slug = SlugUtil::convertSlug($data['name']);
        } else {
            $slug = Locality::DEFAULT_CATALOG_LOCALITY_SLUG;
        }

        $parent = $this->em->getRepository('DomainBusinessBundle:Locality')->getLocalityBySlug($slug);

        return $parent;
    }

    protected function getLocalityItemByName($name)
    {
        $locality = $this->em->getRepository('DomainBusinessBundle:Locality')->findOneBy(['name' => $name]);

        return $locality;
    }

    protected function getBusinessIteratorByLocalityId($id)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->join('b.localities', 'l')
            ->where('b.catalogLocality = :id')
            ->orWhere('l.id = :id')
            ->setParameter(':id', $id)
        ;

        $businesses = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($businesses as $business) {
            $data[] = $business['id'];
        }

        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->where('b.id IN (:ids)')
            ->setParameter('ids', $data)
        ;

        $query = $this->em->createQuery($qb->getDQL());
        $query->setParameter('ids', $data);

        $iterateResult = $query->iterate();

        return $iterateResult;
    }
}
