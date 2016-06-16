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
            case BannerType::CODE_PORTAL:
                break;
            case BannerType::CODE_PORTAL_LEADERBOARD:
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

    protected function getHomepageBanner() : Banner
    {
        //temporaty logic
        return $this->em->getRepository('DomainBannerBundle:Banner')->getBannerByTypeCode(BannerType::CODE_HOME)[0];
    }

    protected function getPortalBanner() : Banner
    {
        //temporaty logic
        return $this->em->getRepository('DomainBannerBundle:Banner')->getBannerByTypeCode(BannerType::CODE_PORTAL)[0];
    }
}
