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
class Version20170105115704 extends AbstractMigration implements ContainerAwareInterface
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

            if ($parent && !empty($parentItem['lat']) and !empty($parentItem['long'])) {
                $parent->setLatitude($parentItem['lat']);
                $parent->setLongitude($parentItem['long']);
            }

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

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function getLocalitiesDeleteList()
    {
        $delete = array (
            109 =>
                array (
                    'id' => '109',
                    'name' => 'Fenton',
                    'parent' => '',
                ),
            83 =>
                array (
                    'id' => '83',
                    'name' => 'Puerto Rico',
                    'parent' => '',
                ),
            92 =>
                array (
                    'id' => '92',
                    'name' => 'Añasco P.R.',
                    'parent' => '6',
                ),
            85 =>
                array (
                    'id' => '85',
                    'name' => 'Bayamon P.R.',
                    'parent' => '11',
                ),
            115 =>
                array (
                    'id' => '115',
                    'name' => 'Bayamón, PR',
                    'parent' => '11',
                ),
            120 =>
                array (
                    'id' => '120',
                    'name' => 'Bayamóm',
                    'parent' => '11',
                ),
            94 =>
                array (
                    'id' => '94',
                    'name' => 'Canóvana',
                    'parent' => '14',
                ),
            123 =>
                array (
                    'id' => '123',
                    'name' => 'Canóvanas, PR',
                    'parent' => '14',
                ),
            121 =>
                array (
                    'id' => '121',
                    'name' => 'Corozal, PR',
                    'parent' => '23',
                ),
            95 =>
                array (
                    'id' => '95',
                    'name' => 'Guayama, PR',
                    'parent' => '29',
                ),
            93 =>
                array (
                    'id' => '93',
                    'name' => 'guaynabo',
                    'parent' => '31',
                ),
            110 =>
                array (
                    'id' => '110',
                    'name' => 'Guaynabo PR',
                    'parent' => '31',
                ),
            111 =>
                array (
                    'id' => '111',
                    'name' => 'Guaynabo, P.R.',
                    'parent' => '31',
                ),
            117 =>
                array (
                    'id' => '117',
                    'name' => 'Trujillo Alto (1semana/mes en Guaynabo)',
                    'parent' => '31',
                ),
            82 =>
                array (
                    'id' => '82',
                    'name' => 'Guyanabo',
                    'parent' => '31',
                ),
            105 =>
                array (
                    'id' => '105',
                    'name' => 'Hatillo PR',
                    'parent' => '33',
                ),
            116 =>
                array (
                    'id' => '116',
                    'name' => 'Hato Rey, San Juan',
                    'parent' => '88',
                ),
            100 =>
                array (
                    'id' => '100',
                    'name' => 'Hormiguero',
                    'parent' => '34',
                ),
            84 =>
                array (
                    'id' => '84',
                    'name' => 'Hormiqueros',
                    'parent' => '34',
                ),
            97 =>
                array (
                    'id' => '97',
                    'name' => 'Levitown',
                    'parent' => '114',
                ),
            118 =>
                array (
                    'id' => '118',
                    'name' => 'Peñuela',
                    'parent' => '56',
                ),
            108 =>
                array (
                    'id' => '108',
                    'name' => 'San Antonio , Texas',
                    'parent' => '79',
                ),
            99 =>
                array (
                    'id' => '99',
                    'name' => 'San Germán P.R.',
                    'parent' => '63',
                ),
            107 =>
                array (
                    'id' => '107',
                    'name' => 'San Germán, PR',
                    'parent' => '63',
                ),
            81 =>
                array (
                    'id' => '81',
                    'name' => 'San Juan,PR',
                    'parent' => '64',
                ),
            103 =>
                array (
                    'id' => '103',
                    'name' => 'San Juan, P. R.',
                    'parent' => '64',
                ),
            90 =>
                array (
                    'id' => '90',
                    'name' => 'Urb. Villa Nevarez, San Juan',
                    'parent' => '64',
                ),
            104 =>
                array (
                    'id' => '104',
                    'name' => 'Viejo San Juan',
                    'parent' => '64',
                ),
        );

        return $delete;
    }

    protected function getParentList()
    {
        $parent = array (
            6 =>
                array (
                    'id' => '6',
                    'name' => 'Anasco',
                ),
            11 =>
                array (
                    'id' => '11',
                    'name' => 'Bayamon',
                ),
            14 =>
                array (
                    'id' => '14',
                    'name' => 'Canovanas',
                ),
            23 =>
                array (
                    'id' => '23',
                    'name' => 'Corozal',
                ),
            29 =>
                array (
                    'id' => '29',
                    'name' => 'Guayama',
                ),
            31 =>
                array (
                    'id' => '31',
                    'name' => 'Guaynabo',
                ),
            33 =>
                array (
                    'id' => '33',
                    'name' => 'Hatillo',
                ),
            88 =>
                array (
                    'id' => '88',
                    'name' => 'Hato Rey',
                ),
            34 =>
                array (
                    'id' => '34',
                    'name' => 'Hormigueros',
                    'lat' => '18.139722',
                    'long' => '-67.1275',
                ),
            114 =>
                array (
                    'id' => '114',
                    'name' => 'Levittown',
                ),
            56 =>
                array (
                    'id' => '56',
                    'name' => 'Penuelas',
                ),
            79 =>
                array (
                    'id' => '79',
                    'name' => 'San Antonio',
                ),
            63 =>
                array (
                    'id' => '63',
                    'name' => 'San German',
                ),
            64 =>
                array (
                    'id' => '64',
                    'name' => 'San Juan',
                ),
        );

        return $parent;
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
            ->select('b.id')
            ->distinct()
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->join('b.localities', 'l')
            ->join('b.catalogLocality', 'cl')
            ->andWhere('cl.id = :id')
            ->orWhere('l.id = :id')
            ->setParameter('id', $id)
        ;

        $businessesIds = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($businessesIds as $row) {
            $data[] = $row['id'];
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
