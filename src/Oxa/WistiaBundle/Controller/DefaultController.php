<?php

namespace Oxa\WistiaBundle\Controller;

use Oxa\WistiaBundle\Manager\WistiaMediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $params = [
            'videos' => $this->getWistiaMediaManager()->getActiveVideos(),
        ];

        return $this->render('OxaWistiaBundle:WistiaMedia:list.html.twig', $params);
    }

    /**
     * @return WistiaMediaManager
     */
    private function getWistiaMediaManager() : WistiaMediaManager
    {
        return $this->get('oxa.manager.wistia_media');
    }
}
