<?php

namespace Domain\BannerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BannerBundle\Entity\Banner;
use Domain\BannerBundle\Model\TypeModel;
use Domain\BannerBundle\Repository\BannerRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BannerManager
{
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
     * @param array $banners
     *
     * @return array
     */
    public function getBanners(array $banners)
    {
        $bannerCodes = $this->checkBannerCodes($banners);

        $data = $this->getRepository()->getBannersByTypeCodes($bannerCodes);

        return $this->prepareBannerData($data);
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    protected function checkBannerCodes(array $banners)
    {
        $data = [];

        foreach ($banners as $bannerKey) {
            if (empty($data[$bannerKey]) and in_array($bannerKey, TypeModel::getBannerTypes())) {
                $data[$bannerKey] = $bannerKey;
            }
        }

        return $data;
    }

    /**
     * @param Banner[] $banners
     *
     * @return array
     */
    protected function prepareBannerData($banners)
    {
        $bannersSizeData = TypeModel::getCodeSizeData();
        $data = [];

        foreach ($banners as $banner) {
            $code = $banner->getCode();
            $isMobile = false;

            if ($code == TypeModel::CODE_SEARCH_FLOAT_BOTTOM) {
                $isMobile = true;
            }

            $data[$code] = [
                'htmlId'        => $banner->getHtmlId(),
                'slotId'        => $banner->getSlotId(),
                'sizes'         => $bannersSizeData[$code],
                'type'          => TypeModel::getBannerResizableTypeByCode($code),
                'isMobile'      => $isMobile,
                'isPublished'   => $banner->getIsPublished(),
            ];
        }

        return $data;
    }

    /**
     * @return BannerRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository(Banner::class);
    }
}
