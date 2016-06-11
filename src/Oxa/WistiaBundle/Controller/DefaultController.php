<?php

namespace Oxa\WistiaBundle\Controller;

use Oxa\WistiaBundle\Manager\WistiaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $wistiaManager = $this->get('oxa.manager.wistia');

        echo '<pre>';

        /**************************************************************/

        //$listProjectsResult = $wistiaManager->listProjects();
        //$showProjectResult = $wistiaManager->showProject('yjgjr3yrzg');

        /*$createProjectResult   = $wistiaManager->createProject([
            'name'                 => 'Test project creation',
            'adminEmail'           => 'xedinaska@gmail.com',
            'anonymousCanUpload'   => 0,
            'anonymousCanDownload' => 1,
            'public'               => 0,
        ]);*/

        /*$updateProjectResult = $wistiaManager->updateProject(
            'n8mgzgnvk6',
            [
                'name'                 => 'Test project update',
                'anonymousCanUpload'   => 1,
                'anonymousCanDownload' => 1,
                'public'               => 1,
            ]
        );*/

        //$removedProjectResult = $wistiaManager->removeProject('g7cbntqvxa');

        //$copiedProjectResult = $wistiaManager->copyProject('elfpwdvli3');

        /**************************************************************/

        //$listMediasResult = $wistiaManager->listMedia();
        //$showMediaResult = $wistiaManager->showMedia('ns9yg0b0c7');
        /*$updateMediaResult = $wistiaManager->updateMedia(
            'ns9yg0b0c7',
            [
                'name' => 'Love Story',
            ]
        );*/

        //$removedMediaResult = $wistiaManager->removeMedia('qyhrzgc30o');
        //$copiedMediaResult = $wistiaManager->copyMedia('ns9yg0b0c7');
        //$statsMediaResult = $wistiaManager->statsMedia('ns9yg0b0c7');

        //var_dump($statsMediaResult);

        //$dummyVideoURL = 'https://d1wst0behutosd.cloudfront.net/videos/9551051/25636925.480p.mp4?Expires=1465657550&Signature=iZ7oOEy-DXBXbDSpvMQnZ9sBOuuCWOb9-ht-26nbBHaGicjvV-CBmyew2iWMi5b7jXw7UvD345OL08paeIgcis0hCDyi-HYaxt0k0kchC09K~SRGfK8mwlVljgZ4c1Fng2snTSH7aNf~EQej~zGgS4r8Ng861ykliH6meZ4GsWwlUtta8PpmcP3Y0e70G27QjE-iNNj8W9zh6gY2J6xvaOgpOL63vxxLN8rOMzCmzKSGlQ1PAU6AxSv9gEYG9c9reqCTfHrHKS3xQRyErmSH759w~UCr78UTEEVjBKVZNjCt50mLQx2YnYmnYEHFHUoiHgtnPHCCH0TWQ--Fny3mzA__&Key-Pair-Id=APKAJJ6WELAPEP47UKWQ';

        die();
        return $this->render('OxaWistiaBundle:Default:index.html.twig');
    }
}
