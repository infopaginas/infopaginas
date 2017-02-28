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
class Version20170105143049 extends AbstractMigration implements ContainerAwareInterface
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

    protected function getLocalitiesDeleteList()
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
            98 =>
                array (
                    'id' => '98',
                    'name' => 'villalba',
                    'parent' => '75',
                ),
        );

        return $delete;
    }

    protected function getParentList()
    {
        $parent = array (
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
            31 =>
                array (
                    'id' => '31',
                    'name' => 'Guaynabo',
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
