<?php

namespace Domain\BannerBundle\Model\Banner;

use Oxa\ManagerArchitectureBundle\Model\Factory\Factory;
use Domain\BannerBundle\Model\TypeInterface as BannerType;

use Domain\BannerBundle\Entity\Banner;

class BannerFactory extends Factory
{
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
            case BannerType::CODE_PORTAL_LEADERBOARD:
                $banner = $this->getLeaderboardBanner();
                break;
            case BannerType::CODE_SERP_BANNER:
                break;
            case BannerType::CODE_SERP_BOXED:
                break;
            case BannerType::CODE_SERP_FEATUREAD:
                break;
            case BannerType::CODE_SERP_MOBILE_TOP:
                break;
            default:
                // TODO: proper exception type/message
                throw new Exception('Undefined type');
        }

        return $banner;
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
}
