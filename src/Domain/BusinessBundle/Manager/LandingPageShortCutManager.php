<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\LandingPageShortCut;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Repository\LandingPageShortCutRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LandingPageShortCutManager
{
    const SHORT_CUT_ITEMS_LANDING = 'LANDING';
    const SHORT_CUT_ITEMS_ERROR   = 'ERROR';

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getLandingPageShortCutItems($locale)
    {
        $shortCutItemsData = $this->prepareShortCutItems($this->getRepository()->getAvailableShortCutItems(), $locale);

        return $shortCutItemsData;
    }

    /**
     * @param LandingPageShortCut[] $shortCutItems
     * @param string $locale
     *
     * @return array
     */
    protected function prepareShortCutItems($shortCutItems, $locale)
    {
        $currentLocale = ucfirst($locale);

        $data = [];

        foreach ($shortCutItems as $item) {
            if ($item->getLocality()) {
                $localityTitle  = $item->getLocality()->getName();
                $localitySearch = $localityTitle;
            } else {
                $localityTitle  = Locality::ALL_LOCALITY_NAME;
                $localitySearch = Locality::ALL_LOCALITY;
            }

            $itemsData = [];

            foreach ($item->getSearchItems() as $searchItem) {
                $itemsData[] = [
                    'title' => $searchItem->{'getTitle' . $currentLocale}(),
                    'data' => [
                        'q'     => $searchItem->{'getSearchText' . $currentLocale}(),
                        'geo'   => $localitySearch,
                    ],
                ];
            }

            $data[] = [
                'title' => $localityTitle,
                'data' => $itemsData,
            ];
        }

        return $data;
    }

    /**
     * @return LandingPageShortCutRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository(LandingPageShortCut::class);
    }
}
