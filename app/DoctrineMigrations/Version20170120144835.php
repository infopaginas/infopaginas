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
class Version20170120144835 extends AbstractMigration implements ContainerAwareInterface
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

                $batchSize = 20;
                $i = 0;

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

                    if (($i % $batchSize) === 0) {
                        $this->em->flush();
                        $this->em->clear();

                        $parent = $this->getParent($parentItem);
                        $locality = $this->getLocalityItemByName($item['name']);
                    }
                    $i ++;

                    $this->em->detach($row[0]);
                }

                $this->em->remove($locality);
            }
        }

        $updateCoordinate = $this->getLocalitiesCoordinateUpdate();

        foreach ($updateCoordinate as $item) {
            if (!empty($item['name']) and !empty($item['latitude']) and !empty($item['longitude'])) {
                $locality = $this->getLocalityItemByName($item['name']);

                $locality->setLatitude($item['latitude']);
                $locality->setLongitude($item['longitude']);
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
            12 => [
                'id' => '12',
                'name' => 'Cabo Rojo',
            ],
            64 => [
                'id' => '64',
                'name' => 'San Juan',
            ],
            11 => [
                'id' => '11',
                'name' => 'Bayamon',
            ],
            3 => [
                'id' => '3',
                'name' => 'Aguadilla',
            ],
            52 => [
                'id' => '52',
                'name' => 'Naguabo',
            ],
            13 => [
                'id' => '13',
                'name' => 'Caguas',
            ],
            70 => [
                'id' => '70',
                'name' => 'Trujillo Alto',
            ],
        ];

        return $parent;
    }

    protected function getLocalitiesDeleteList()
    {
        $delete = [
            101 => [
                'id' => '101',
                'name' => 'Boquerón',
                'parent' => '12',
            ],
            80 => [
                'id' => '80',
                'name' => 'Cupey',
                'parent' => '64',
            ],
            113 => [
                'id' => '113',
                'name' => 'Hato Rey',
                'parent' => '64',
            ],
            124 => [
                'id' => '124',
                'name' => 'Hato Tejas',
                'parent' => '11',
            ],
            89 => [
                'id' => '89',
                'name' => 'Isla Verde',
                'parent' => '64',
            ],
            114 => [
                'id' => '114',
                'name' => 'Levittown',
                'parent' => '64',
            ],
            106 => [
                'id' => '106',
                'name' => 'Miramar',
                'parent' => '64',
            ],
            91 => [
                'id' => '91',
                'name' => 'Ramey',
                'parent' => '3',
            ],
            119 => [
                'id' => '119',
                'name' => 'Reston',
                'parent' => '64',
            ],
            102 => [
                'id' => '102',
                'name' => 'Rio Blanco',
                'parent' => '52',
            ],
            87 => [
                'id' => '87',
                'name' => 'Rio Piedras',
                'parent' => '64',
            ],
            79 => [
                'id' => '79',
                'name' => 'San Antonio',
                'parent' => '13',
            ],
            86 => [
                'id' => '86',
                'name' => 'Santurce',
                'parent' => '64',
            ],
            112 => [
                'id' => '112',
                'name' => 'St. Just Station',
                'parent' => '70',
            ],
        ];

        return $delete;
    }

    protected function getLocalitiesCoordinateUpdate()
    {
        $data = [
            27 => [
                'id'        => '27',
                'name'      => 'Florida',
                'latitude'  => 18.363611,
                'longitude' => -66.571389,
            ],
        ];

        return $data;
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
