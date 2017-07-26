<?php

namespace Domain\BannerBundle\Model\Banner;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Domain\BannerBundle\Model\TypeModel;
use Oxa\ManagerArchitectureBundle\Model\Factory\Factory;
use Domain\BannerBundle\Model\TypeInterface as BannerType;
use Domain\BannerBundle\Entity\Banner;

class BannerFactory extends Factory
{
    const UNDEFINED_BANNER_TYPE_ERROR = 'Undefined banner type!';

    /**
     * @var ArrayCollection
     */
    protected $bannersCollection;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->bannersCollection = new ArrayCollection;
    }

    /**
     * @param array $banners
     * @throws \Exception
     */
    public function prepareBanners(array $banners)
    {
        foreach ($banners as $bannerKey) {
            if ($this->bannersCollection->containsKey($bannerKey)) {
                throw new \Exception(sprintf("Banner type %s already loaded", $bannerKey), 1);
            }

            $this->bannersCollection->set($bannerKey, $this->get($bannerKey));
        }
    }

    /**
     * @param int $type
     *
     * @return Banner|null
     */
    public function get($type)
    {
        $banner = null;

        if (in_array($type, TypeModel::getBannerTypes())) {
            $banner = $this->getBannerByCode($type);
        }

        return $banner;
    }

    /**
     * @param int $type
     *
     * @return Banner|null
     * @throws \Exception
     */
    public function retrieve($type)
    {
        if ($this->bannersCollection->containsKey($type)) {
            return $this->bannersCollection->get($type);
        } else {
            throw new \Exception(sprintf("Banners with type %s have not been loaded.", $type), 1);
        }
    }

    /**
     * @return array|null
     */
    public function getItemsHeaders()
    {
        return array_map(
            function ($item) {
                if (null !== $item && null !== $item->getTemplate() and null !== $item->getType()) {
                    $bannerTypeCode = $item->getType()->getCode();

                    if (in_array($bannerTypeCode, TypeModel::getBannerResizable())) {
                        $header = $item->getTemplate()->getResizableHeader();
                    } elseif (in_array($bannerTypeCode, TypeModel::getBannerResizableInBlock())) {
                        $header = $item->getTemplate()->getResizableInBlockHeader();
                    } else {
                        $header = $item->getTemplate()->getTemplateHeader();
                    }

                    return [
                        'data' => $header,
                        'code' => $bannerTypeCode,
                    ];
                }

                return null;
            },
            $this->bannersCollection->toArray()
        );
    }

    /**
     * @param int $code
     *
     * @return Banner|null
     */
    protected function getBannerByCode($code)
    {
        $banners = $this->em->getRepository(Banner::class)->getBannerByTypeCode($code);

        if (count($banners)) {
            return $banners[0];
        }
        return null;
    }
}
