<?php

namespace Oxa\WistiaBundle\Controller;

use Oxa\WistiaBundle\Form\FileUploadType;
use Oxa\WistiaBundle\Form\UrlUploadType;
use Oxa\WistiaBundle\Manager\WistiaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WistiaMediaController extends Controller
{
    public function uploadLocalFileAction(Request $request)
    {
        $form = $this->createForm(new FileUploadType());

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $filename = $form['file']->getData()->getClientOriginalName();
                $file = $form['file']->getData()->getRealPath();

                $media = $this->getWistiaManager()->uploadLocalFile($file, ['name' => $filename]);

                dump($media);
                die();
            }
        }

        return $this->render('OxaWistiaBundle:WistiaMedia:upload_local_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function uploadRemoteFileAction(Request $request)
    {
        $form = $this->createForm(new UrlUploadType());

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $url = $form['url']->getData();

                $media = $this->getWistiaManager()->uploadRemoteFile($url);

                dump($media);
                die();
            }
        }

        return $this->render('OxaWistiaBundle:WistiaMedia:upload_remote_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function viewAction(Request $request, int $id)
    {
        $wistiaMediaManager = $this->get('oxa.manager.wistia_media');

        $storedMediaData = $wistiaMediaManager->find($id);

        $embed = $this->getWistiaManager()->getEmbedCode($storedMediaData->getHashedId(), ['width' => 200]);

        return $this->render('OxaWistiaBundle:WistiaMedia:view.html.twig', [
            'embed' => $embed,
        ]);
    }

    private function getWistiaManager() : WistiaManager
    {
        return $this->get('oxa.manager.wistia');
    }
}
