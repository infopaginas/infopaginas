<?php

namespace Oxa\WistiaBundle\Controller;

use Oxa\WistiaBundle\Manager\WistiaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WistiaMediaController extends Controller
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

        /*$localFileUploadData = [
            'file' => '/var/www/symfony/dummy.mp4',
        ];

        $localFileUploadResult = $wistiaLocalFileUploader->upload();*/


        /*$remoteFileUploadResult = $wistiaManager->uploadRemoteFile('http://www.w3schools.com/html/mov_bbb.mp4', [
            'name' => 'test',
        ]);*/

        $localFileUploadResult = $wistiaManager->uploadLocalFile('/var/www/symfony/dummy.mp4', ['description' => 'dum']);

        var_dump($localFileUploadResult->getId());

        die();
        return $this->render('OxaWistiaBundle:Default:index.html.twig');
    }

    public function uploadLocalFileAction(Request $request)
    {

    }

    public function uploadRemoteFileAction(Request $request)
    {

    }

    public function viewAction(Request $request, int $id)
    {
        
    }
}
