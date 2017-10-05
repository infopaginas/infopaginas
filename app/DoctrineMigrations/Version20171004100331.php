<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\EmergencyBundle\Entity\EmergencyAbstractBusiness;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20171004100331 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em        = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $areaName = 'Central-Este';

        $mainAreaSlug   = 'centro';
        $mergedAreaSlug = 'este';

        $mergedArea = $this->getAreaBySlug($mergedAreaSlug);
        $mainArea   = $this->getAreaBySlug($mainAreaSlug);

        $mainArea->setName($areaName);
        $mainArea->setSlug(null);

        // update businesses
        $this->updateBusinessesArea($mergedArea->getBusinesses(), $mainArea);

        // update draft businesses
        $this->updateBusinessesArea($mergedArea->getDraftBusinesses(), $mainArea);

        $this->em->remove($mergedArea);
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    /**
     * @param EmergencyAbstractBusiness[] $businesses
     * @param EmergencyArea $area
     */
    protected function updateBusinessesArea($businesses, $area)
    {
        foreach ($businesses as $business) {
            $business->setArea($area);
        }
    }

    /**
     * @param string $slug
     *
     * @return EmergencyArea|null
     */
    protected function getAreaBySlug($slug)
    {
        return $this->em->getRepository(EmergencyArea::class)->findOneBy([
            'slug' => $slug,
        ]);
    }
}
