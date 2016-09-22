<?php

namespace Domain\BannerBundle\Model\Banner;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Oxa\ManagerArchitectureBundle\Model\Factory\Factory;
use Domain\BannerBundle\Model\TypeInterface as BannerType;

use Domain\BannerBundle\Entity\Banner;

class BannerFactory extends Factory
{
    const UNDEFINED_BANNER_TYPE_ERROR = 'Undefined banner type!';

    protected $bannersCollection;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->bannersCollection = new ArrayCollection;
    }

    public function prepearBanners(array $banners)
    {
        foreach ($banners as $bannerKey) {
            if ($this->bannersCollection->containsKey($bannerKey)) {
                throw new \Exception(sprintf("Banner type %s already loaded", $bannerKey), 1);
            }

            $this->bannersCollection->set($bannerKey, $this->get($bannerKey));
        }
    }

    public function get($type)
    {
        $banner = null;
        switch ($type) {
            case BannerType::CODE_HOME:
                $banner = $this->getHomepageBanner();
                break;
            case BannerType::CODE_PORTAL:
                $banner = $this->getPortalBanner();
                break;
            case BannerType::CODE_PORTAL_LEFT:
                $banner = $this->getPortalLeftBanner();
                break;
            case BannerType::CODE_PORTAL_RIGHT:
                $banner = $this->getPortalRightBanner();
                break;
            case BannerType::CODE_PORTAL_LEADERBOARD:
                $banner = $this->getLeaderboardBanner();
                break;
            case BannerType::CODE_PORTAL_RIGHT_MOBILE:
                $banner = $this->getPortalRightMobileBanner();
                break;
            case BannerType::CODE_PORTAL_LEFT_MOBILE:
                $banner = $this->getPortalLeftMobileBanner();
                break;
            case BannerType::CODE_SERP_BANNER:
                $banner = $this->getHomepageLargeBanner();
                break;
            case BannerType::CODE_SERP_BOXED:
                $banner = $this->getProfileBanner();
                break;
            case BannerType::CODE_SERP_FEATUREAD:
                break;
            case BannerType::CODE_SERP_MOBILE_TOP:
                break;
            default:
                throw new \Exception(self::UNDEFINED_BANNER_TYPE_ERROR);
        }

        return $banner;
    }

    public function retrieve($type)
    {
        if ($this->bannersCollection->containsKey($type)) {
            return $this->bannersCollection->get($type);
        } else {
            throw new \Exception(sprintf("Banners with type %s have not been loaded.", $type), 1);
        }
    }

    public function getItemsHeaders()
    {
        return array_map(
            function ($item) {
                if (null !== $item && null !== $item->getTemplate()) {
                    return $item->getTemplate()->getTemplateHeader();
                }
                return null;
            },
            $this->bannersCollection->toArray()
        );
    }

    protected function getHomepageBanner()
    {
        //temporaty logic
        $homapageBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_HOME);

        if (count($homapageBanners)) {
            return $homapageBanners[0];
        }
        return null;
    }

    protected function getPortalBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getPortalLeftBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL_LEFT);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getPortalRightBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL_RIGHT);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getPortalLeftMobileBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL_LEFT_MOBILE);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getPortalRightMobileBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL_RIGHT_MOBILE);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getLeaderboardBanner()
    {
        //temporaty logic
        $leaderboardBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_PORTAL_LEADERBOARD);

        if (count($leaderboardBanners)) {
            return $leaderboardBanners[0];
        }

        return null;
    }

    protected function getProfileBanner()
    {
        $portalBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_SERP_BOXED);

        if (count($portalBanners)) {
            return $portalBanners[0];
        }
        return null;
    }

    protected function getHomepageLargeBanner()
    {
        $homepageBanners = $this->em->getRepository('DomainBannerBundle:Banner')
            ->getBannerByTypeCode(BannerType::CODE_SERP_BANNER);

        if (count($homepageBanners)) {
            return $homepageBanners[0];
        }

        return null;
    }
}
