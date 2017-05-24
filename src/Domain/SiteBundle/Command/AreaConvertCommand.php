<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Locality;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class AreaConvertCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    protected function configure()
    {
        $this->setName('data:area-mapping:convert');
        $this->setDescription('Areas conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->updateLocalitiesArea();
        $this->updateBusinessLocalities();

        $this->em->flush();
    }

    protected function updateLocalitiesArea()
    {
        $updateLocalityList = $this->getUpdateLocalityList();

        foreach ($updateLocalityList as $localitySlug => $areaName) {
            $locality = $this->getLocalityBySlug($localitySlug);
            $area     = $this->getAreaByName($areaName);

            if ($locality and !$locality->getArea() and $area) {
                $locality->setArea($area);
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    protected function updateBusinessLocalities()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getActiveBusinessProfilesIterator();

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];

            if (!$business->getNeighborhoods()->isEmpty()) {
                foreach ($business->getNeighborhoods() as $neighborhood) {
                    $locality = $neighborhood->getLocality();

                    if ($locality and !$business->getLocalities()->contains($locality)) {
                        $business->addLocality($locality);
                    }
                }
            }

            if (!$business->getLocalities()->isEmpty()) {
                foreach ($business->getLocalities() as $locality) {
                    $area = $locality->getArea();

                    if ($area and !$business->getAreas()->contains($area)) {
                        $business->addArea($area);
                    }
                }
            }

            $this->em->flush();
            $this->em->clear();
        }
    }

    /**
     * @param string $slug
     *
     * @return Locality|null
     */
    protected function getLocalityBySlug($slug)
    {
        $locality = $this->em->getRepository(Locality::class)->findOneBy(['slug' => $slug]);

        return $locality;
    }

    /**
     * @param string $name
     *
     * @return Area|null
     */
    protected function getAreaByName($name)
    {
        $area = $this->em->getRepository(Area::class)->findOneBy(['name' => $name]);

        return $area;
    }

    protected function getUpdateLocalityList()
    {
        $localities = [
            'santa-isabel'  => 'South',
            'santurce'      => 'Metro',
            'camuy'         => 'North',
            'canovana'      => 'East',
            'hato-rey'      => 'Metro',
            'levittown'     => 'Metro',
            'rio-piedras'   => 'Metro',
        ];

        return $localities;
    }
}
