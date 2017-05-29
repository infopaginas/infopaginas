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
        $this->removeArea();

        $this->em->flush();
    }

    protected function updateLocalitiesArea()
    {
        $updateLocalityList = $this->getUpdateLocalityList();

        foreach ($updateLocalityList as $areaName => $localities) {
            $area = $this->getAreaByName($areaName);

            if ($area) {
                foreach ($localities as $localitySlug) {
                    $locality = $this->getLocalityBySlug($localitySlug);

                    if ($locality) {
                        $locality->setArea($area);
                    }
                }
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

    protected function removeArea()
    {
        $removeAreaList = $this->getRemoveAreaList();

        foreach ($removeAreaList as $areaName) {
            $area = $this->getAreaByName($areaName);

            if ($area) {
                $this->em->remove($area);
            }
        }

        $this->em->flush();
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
            'Metro' => [
                // main
                'dorado',
                'catano',
                'toa-alta',
                'toa-baja',
                'guaynabo',
                'san-juan',
                'carolina',
                'trujillo-alto',
                'bayamon',

                // from api
                'santurce',
                'hato-rey',
                'levittown',
                'rio-piedras',
            ],
            'Central' => [
                'loiza',
                'rio-grande',
                'canovanas',
                'luquillo',
                'fajardo',
                'ceiba',
                'naguabo',
                'humacao',
                'yabucoa',
                'las-piedras',
                'juncos',
                'san-lorenzo',
                'caguas',
                'aguas-buenas',
                'cidra',
                'cayey',
                'aibonito',
                'gurabo',
                'culebra',
                'vieques',
            ],
            'North' => [
                'quebradillas',
                'hatillo',
                'camuy',
                'lares',
                'arecibo',
                'utuado',
                'barceloneta',
                'florida',
                'manati',
                'vega-baja',
                'vega-alta',
                'morovis',
                'ciales',
                'orocovis',
                'corozal',
                'naranjito',
                'barranquitas',
            ],
            'South' => [
                'adjuntas',
                'penuelas',
                'guayanilla',
                'yauco',
                'guanica',
                'jayuya',
                'ponce',
                'villalba',
                'juana-diaz',
                'coamo',
                'santa-isabel',
                'salinas',
                'guayama',
                'patillas',
                'arroyo',
                'maunabo',
            ],
            'West' => [
                'aguadilla',
                'isabela',
                'moca',
                'aguada',
                'san-sebastian',
                'rincon',
                'anasco',
                'las-marias',
                'mayaguez',
                'maricao',
                'hormigueros',
                'san-german',
                'cabo-rojo',
                'lajas',
                'sabana-grande',
            ],
        ];

        return $localities;
    }

    protected function getRemoveAreaList()
    {
        return [
            'East',
        ];
    }
}
