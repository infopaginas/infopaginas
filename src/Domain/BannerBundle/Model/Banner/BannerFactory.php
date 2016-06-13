<?php

namespace Domain\BannerBundle\Model\Banner\Factory;

use Oxa\ManagerArchitectureBundle\Model\Factory\Factory;
use Domain\BannerBundle\Model\TypeInterface as BannerType;

class BannerFactory extends Factory
{
    public function get($type)
    {
        $banner = null;
        switch ($type) {
            case BannerType::CODE_HOME:
                $this->getHomepageBanner();
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
    }

    protected function getHomepageBanner()
    {
        
    }
}
