<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Oxa\WistiaBundle\Form\Handler\FileUploadFormHandler;
use Oxa\WistiaBundle\Form\Handler\RemoteFileUploadFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VideosController
 * @package Domain\BusinessBundle\Controller
 */
class VideosController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_UPLOADED_MESSAGE = 'Video has been successfully uploaded.';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const BUSINESS_PROFILE_ID_PARAMNAME = 'businessProfileId';


    public function localFileUploadAction(Request $request)
    {
        $businessProfileId = (int)$request->get(self::BUSINESS_PROFILE_ID_PARAMNAME, 0);

        $business = $this->getBusinessProfilesManager()->find($businessProfileId);

        if ($business === null) {
            $this->throwBusinessNotFoundException();
        }

        /** @var UploadedFile $file */
        foreach ($request->files->get('files') as $file) {
            $filename = $file->getClientOriginalName();
            $path = $file->getRealPath();

            $media = $this->get('oxa.manager.wistia')->uploadLocalFile($path, ['name' => $filename]);
        }

        return $this->render('DomainBusinessBundle:Videos/blocks:video.html.twig', [
            'media' => $media,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function remoteFileUploadAction()
    {
        $formHandler = $this->getRemoteFileUploadFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_UPLOADED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], 500);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return FileUploadFormHandler
     */
    private function getLocalFileUploadFormHandler() : FileUploadFormHandler
    {
        return $this->get('domain_business.form.handler.local_file_upload');
    }

    /**
     * @return RemoteFileUploadFormHandler
     */
    private function getRemoteFileUploadFormHandler() : RemoteFileUploadFormHandler
    {
        return $this->get('domain_business.form.handler.url_file_upload');
    }

    /**
     * @access private
     * @throws NoResultException
     */
    private function throwBusinessNotFoundException()
    {
        throw new NoResultException('Business not found');
    }

    /**
     * @return \Domain\BusinessBundle\Manager\BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }
}
