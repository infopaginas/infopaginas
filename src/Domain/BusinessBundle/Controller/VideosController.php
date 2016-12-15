<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\ORM\NoResultException;
use Domain\BannerBundle\Model\TypeInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\VideoManager;
use Domain\BusinessBundle\Model\DataType\ReviewsListQueryParamsDTO;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Domain\BusinessBundle\Util\Traits\VideoUploadTrait;
use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Form\Handler\FileUploadFormHandler;
use Oxa\VideoBundle\Form\Handler\RemoteFileUploadFormHandler;
use Oxa\VideoBundle\Form\Type\VideoMediaType;
use Oxa\VideoBundle\Manager\VideoManager as OxaVideoManager;
use Oxa\VideoBundle\Manager\VideoMediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VideosController
 * @package Domain\BusinessBundle\Controller
 */
class VideosController extends Controller
{
    use JsonResponseBuilderTrait;
    use VideoUploadTrait;

    const SUCCESS_UPLOADED_MESSAGE = 'Video has been successfully uploaded.';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const BUSINESS_PROFILE_ID_PARAMNAME = 'businessProfileId';

    const BUSINESS_NOT_FOUND_MESSAGE = 'Business profile is not found.';

    const FILE_NOT_PROVIDED_MESSAGE = 'Videofile is not provided.';

    public function localFileUploadAction(Request $request)
    {
        $business = $this->getBusinessProfileFromRequestData($request);

        try {
            if ($business === null) {
                $this->throwBusinessNotFoundException();
            }

            $files = $request->files->get('files');

            if (empty($files)) {
                $this->throwVideoFileIsNotProvidedException();
            }

            $media = $this->getVideoAPIManager()->uploadLocalFile(current($files), ['name' => '']);
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], 500);
        }

        $editVideoForm = $this->getEditVideoForm($business, $media);

        $response = $this->renderView(':redesign/blocks/businessProfile/subTabs/profile/gallery:videos.html.twig', [
            'media' => $media,
            'form' => $editVideoForm->createView(),
        ]);

        return $this->getSuccessResponse($response);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function remoteFileUploadAction(Request $request)
    {
        $business = $this->getBusinessProfileFromRequestData($request);

        try {
            $url = $request->get('url');
            $media = $this->getVideoAPIManager()->uploadRemoteFile($url);
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], 500);
        }

        if ($media) {
            $editVideoForm = $this->getEditVideoForm($business, $media);

            $response = $this->renderView(':redesign/blocks/businessProfile/subTabs/profile/gallery:videos.html.twig', [
                'media' => $media,
                'form' => $editVideoForm->createView(),
            ]);

            return $this->getSuccessResponse($response);
        } else {
            return $this->getFailureResponse(
                $this->getTranslator()->trans('business_profile.video.invalid_url', [], 'validators'),
                []
            );
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paramsDTO = $this->geVideoListQueryParamsDTO($request);

        $videoResultDTO = $this->getVideoManager()->getVideosResultDTO($paramsDTO);

        $bannerFactory = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(
            [
                TypeInterface::CODE_PORTAL_RIGHT,
                TypeInterface::CODE_STATIC_BOTTOM,
            ]
        );

        $seoData = $this->getVideoManager()->getVideosSeoData($this->container);

        $params = [
            'results'       => $videoResultDTO,
            'seoData'       => $seoData,
            'bannerFactory' => $bannerFactory,
        ];

        return $this->render(':redesign:video-list.html.twig', $params);
    }

    /**
     * @param Request $request
     * @return ReviewsListQueryParamsDTO
     */
    private function geVideoListQueryParamsDTO(Request $request) : ReviewsListQueryParamsDTO
    {
        $limit = (int)$this->get('oxa_config')->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $page = SearchDataUtil::getPageFromRequest($request);

        return new ReviewsListQueryParamsDTO($limit, $page);
    }

    /**
     * @return VideoManager
     */
    private function getVideoManager() : VideoManager
    {
        return $this->get('domain_business.video');
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
        throw new NoResultException(self::BUSINESS_NOT_FOUND_MESSAGE);
    }

    private function throwVideoFileIsNotProvidedException()
    {
        throw new FileNotFoundException(self::FILE_NOT_PROVIDED_MESSAGE);
    }

    /**
     * @return \Domain\BusinessBundle\Manager\BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @return OxaVideoManager
     */
    private function getVideoAPIManager() : OxaVideoManager
    {
        return $this->get('oxa.manager.video');
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param VideoMedia $media
     * @return FormInterface
     */
    private function getEditVideoForm(BusinessProfile $businessProfile, VideoMedia $media) : FormInterface
    {
        $form = $this->createForm(new BusinessProfileFormType(), $businessProfile);

        $videoForm = $form->get('video')->setData($media);

        return $videoForm;
    }

    /**
     * @param Request $request
     * @return BusinessProfile
     */
    private function getBusinessProfileFromRequestData(Request $request)
    {
        $businessProfileId = (int)$request->get(self::BUSINESS_PROFILE_ID_PARAMNAME, 0);

        /** @var BusinessProfile $business */
        $business = $this->getBusinessProfilesManager()->find($businessProfileId);

        return $business;
    }
}
