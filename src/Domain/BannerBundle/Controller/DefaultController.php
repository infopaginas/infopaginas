<?php

namespace Domain\BannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Domain\BannerBundle\Model\TypeInterface;

class DefaultController extends Controller
{
    public function renderBannerAction($crop)
    {
        $bannerFactory  = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager
        $banner         = $bannerFactory->get($crop);

        return $this->render(
            'DomainBannerBundle:Default:render_banner_tempate.html.twig',
            array(
                'banner' => $banner
            )
        );
    }
}
