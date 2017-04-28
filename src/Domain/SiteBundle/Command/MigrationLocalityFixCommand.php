<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Util\SlugUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationLocalityFixCommand extends ContainerAwareCommand
{
    const DEFAULT_LOCALE  = 'en';

    const API_BASE_URL    = 'http://infopaginas.drxlive.com/api/businesses';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var bool $withDebug
     */
    protected $withDebug;

    /**
     * @var array $categoryEnMergeMapping
     */
    protected $categoryEnMergeMapping;

    /**
     * @var array $categoryEsMergeMapping
     */
    protected $categoryEsMergeMapping;

    protected function configure()
    {
        $this->setName('data:migration:locality-fix');
        $this->setDescription('Update Locality');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        if ($input->getOption('pageStart')) {
            $pageStart = $input->getOption('pageStart');
        } else {
            $pageStart = 1;
        }

        if ($input->getOption('pageCountLimit')) {
            $pageCountLimit = $input->getOption('pageCountLimit');
        } else {
            $pageCountLimit = 1;
        }

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                $output->writeln('Start request page number ' . $page);
            }

            $data = $this->getCurlData($this->getBusinessesByPageUrl($page), self::DEFAULT_LOCALE);

            if ($data) {
                foreach ($data as $item) {
                    $itemId = $item->_id;

                    /* @var BusinessProfile $businessProfile */
                    $businessProfile = $this->em->getRepository(BusinessProfile::class)->findOneBy(
                        [
                            'uid' => $itemId,
                        ]
                    );

                    if ($businessProfile) {
                        $data = $this->getCurlData($this->getBusinessByUid($itemId), self::DEFAULT_LOCALE);

                        $businessProfile = $this->removeOldLocalities($businessProfile);

                        $this->updateBusinessLocalities($item, $data, $businessProfile);

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip item with id ' . $itemId);
                        }
                    }

                    $this->em->flush();
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        if ($this->withDebug) {
            $output->writeln('Finish requests');
        }
    }

    /**
     * @param string $url
     * @param string $locale
     *
     * @return mixed
     */
    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale,
        ]);

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    /**
     * @param mixed             $item
     * @param mixed             $data
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function updateBusinessLocalities($item, $data, $businessProfile)
    {
        $address    = $data->business->address;
        $localities = $this->getLocalitiesFormResponse($item);
        $radius     = $this->getServiceRadiusFormResponse($item);

        if (!$this->checkCatalogLocality($address, $businessProfile)) {
            $catalogLocality = $this->loadLocality($address);
            $businessProfile->setCatalogLocality($catalogLocality);
        }

        if ($localities) {
            $businessProfile->setServiceAreasType(BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE);

            foreach ($localities as $item) {
                $locality = $this->loadLocality($item);
                $businessProfile = $this->handleLocalityServiceType($businessProfile, $locality);
            }
        } elseif ($radius) {
            $businessProfile->setMilesOfMyBusiness($radius);
            $businessProfile->setServiceAreasType(BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE);
        } else {
            $businessProfile->setServiceAreasType(BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE);

            $locality = $businessProfile->getCatalogLocality();
            $businessProfile = $this->handleLocalityServiceType($businessProfile, $locality);
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function removeOldLocalities($businessProfile)
    {
        $neighborhoods = $businessProfile->getNeighborhoods();

        foreach ($neighborhoods as $neighborhood) {
            $businessProfile->removeNeighborhood($neighborhood);
        }

        $localities = $businessProfile->getLocalities();

        foreach ($localities as $locality) {
            $businessProfile->removeLocality($locality);
        }

        $areas = $businessProfile->getAreas();

        foreach ($areas as $area) {
            $businessProfile->removeArea($area);
        }

        return $businessProfile;
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    private function getBusinessesByPageUrl($pageNumber)
    {
        return self::API_BASE_URL . '?page=' . $pageNumber;
    }

    /**
     * @param string $uid
     *
     * @return string
     */
    private function getBusinessByUid($uid)
    {
        return self::API_BASE_URL . '/' . $uid;
    }

    /**
     * @param mixed $item
     *
     * @return Locality
     */
    private function loadLocality($item)
    {
        $slug = $this->getLocalitySlug($item);

        $locality = $this->em->getRepository(Locality::class)->getLocalityBySlug($slug);

        if (!$locality) {
            $locality = new Locality();
            $locality->setName(trim($item->locality));

            $this->em->persist($locality);
            $this->em->flush();
        }

        if (!$locality->getLongitude()) {
            $locality->setLongitude($item->coordinates[0]);
            $locality->setLatitude($item->coordinates[1]);
        }

        return $locality;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    private function getLocalitySlug($item)
    {
        $slug = SlugUtil::convertSlug(trim($item->locality));

        return $slug;
    }

    /**
     * @param BusinessProfile   $businessProfile
     * @param Locality          $locality
     *
     * @return BusinessProfile
     */
    private function handleLocalityServiceType($businessProfile, $locality)
    {
        $area = $locality->getArea();

        $businessProfile->addLocality($locality);

        if ($area and !$businessProfile->getAreas()->contains($area)) {
            $businessProfile->addArea($area);
        }

        if ($locality->getNeighborhoods()) {
            foreach ($locality->getNeighborhoods() as $neighborhood) {
                $businessProfile->addNeighborhood($neighborhood);
            }
        }

        return $businessProfile;
    }

    /**
     * @param mixed $commonData
     *
     * @return array
     */
    private function getLocalitiesFormResponse($commonData)
    {
        $localities = [];

        if (!empty($commonData->service_areas)) {
            foreach ($commonData->service_areas as $locality) {
                $localities[$locality->locality] = $locality;
            }
        }

        return $localities;
    }

    /**
     * @param mixed $commonData
     *
     * @return int
     */
    private function getServiceRadiusFormResponse($commonData)
    {
        if (empty($commonData->radius_served)) {
            $radius = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
        } else {
            $radius = (int)$commonData->radius_served;
        }

        return $radius;
    }

    /**
     * @param mixed             $address
     * @param BusinessProfile   $businessProfile
     *
     * @return bool
     */
    private function checkCatalogLocality($address, $businessProfile)
    {
        $result = false;

        $localitySlug = $this->getLocalitySlug($address);

        if ($businessProfile->getCatalogLocality()->getSlug() == $localitySlug) {
            $result = true;
        }

        return $result;
    }
}
