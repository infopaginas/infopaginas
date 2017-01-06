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
class Version20170106063045 extends AbstractMigration
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
        //clean businesses with wrong catalog locality
        $deleteLocalities = $this->getLocalitiesDeleteIterator();
        $parentList = $this->getParentList();
        $localityMapping = $this->getDeleteLocalityMapping();

        foreach ($deleteLocalities as $localityRow) {
            /* @var $locality Locality */
            $locality = $localityRow[0];
            $parentItem = false;

            if (!empty($localityMapping[$locality->getId()])) {
                if ($localityMapping[$locality->getId()]['parent']) {
                    $parentItem = $parentList[$localityMapping[$locality->getId()]['parent']];
                }
            }

            $parent = $this->getParent($parentItem);

            $businesses = $this->getBusinessIteratorByLocalityId($locality->getId());

            foreach ($businesses as $businessRow) {
                /* @var $business BusinessProfile */
                $business = $businessRow[0];

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

                $this->em->detach($businessRow[0]);
            }

            $this->em->detach($localityRow[0]);
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function getLocalitiesDeleteIterator()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('l')
            ->from('DomainBusinessBundle:Locality', 'l')
            ->where('l.deletedAt IS NOT NULL')
        ;

        $query = $this->em->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
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
            73 =>
                array (
                    'id' => '73',
                    'name' => 'Vega Baja',
                ),
            69 =>
                array (
                    'id' => '69',
                    'name' => 'Toa Baja',
                ),
            75 =>
                array (
                    'id' => '75',
                    'name' => 'Villalba',
                ),
        );

        return $parent;
    }

    protected function getDeleteLocalityMapping()
    {
        $delete = array (
            85 =>
                array (
                    'id' => '85',
                    'name' => 'Bayamon  P.R.',
                    'parent' => '11',
                ),
            94 =>
                array (
                    'id' => '94',
                    'name' => 'Canóvana ',
                    'parent' => '14',
                ),
            111 =>
                array (
                    'id' => '111',
                    'name' => 'Guaynabo, P.R. ',
                    'parent' => '31',
                ),
            99 =>
                array (
                    'id' => '99',
                    'name' => 'San Germán P.R. ',
                    'parent' => '63',
                ),
            104 =>
                array (
                    'id' => '104',
                    'name' => 'Viejo San Juan ',
                    'parent' => '64',
                ),
            122 =>
                array (
                    'id' => '122',
                    'name' => 'Vega Baja, PR',
                    'parent' => '73',
                ),
            96 =>
                array (
                    'id' => '96',
                    'name' => 'Toa Baja PR',
                    'parent' => '69',
                ),
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
            98 =>
                array (
                    'id' => '98',
                    'name' => 'villalba',
                    'parent' => '75',
                ),
        );

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
            ->where('b.catalogLocality = :id')
            ->setParameter(':id', $id)
        ;

        $query = $this->em->createQuery($qb->getDQL());
        $query->setParameter('id', $id);

        $iterateResult = $query->iterate();

        return $iterateResult;
    }
}
