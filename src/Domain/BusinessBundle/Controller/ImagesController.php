<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Manager\BusinessGalleryManager;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImagesController
 * @package Domain\BusinessBundle\Controller
 */
class ImagesController extends Controller
{
    use JsonResponseBuilderTrait;

    const BUSINESS_PROFILE_ID_PARAMNAME = 'businessProfileId';

    const ADMIN_MEDIA_UPLOAD_NO_FILE  = 'media_upload_error_message.no_file';
    const ADMIN_MEDIA_UPLOAD_MAX_SIZE = 'media_upload_error_message.max_size';
    const ADMIN_MEDIA_UPLOAD_INVALID_EXTENSION = 'media_upload_error_message.invalid_extension';
    const ADMIN_MEDIA_PREVIEW_NO_FILE = 'media_preview_error_message.not_found';

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $businessProfileId = (int)$request->get(self::BUSINESS_PROFILE_ID_PARAMNAME, 0);

        $business = $this->getBusinessProfilesManager()->find($businessProfileId);

        if ($business === null) {
            $this->throwBusinessNotFoundException();
        }

        $business = $this->getBusinessGalleryManager()->fillBusinessGallery(
            $business,
            $request->get('context', OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES),
            $request->files
        );

        $imagesForm = $this->getImagesForm($business);

        return $this->render(':redesign/blocks/businessProfile/subTabs/profile/gallery:images.html.twig', [
            'images'     => $imagesForm['images']->createView(),
            'logo'       => $imagesForm['logo']->createView(),
            'background' => $imagesForm['background']->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminGetMediaLinkAction(Request $request, $id)
    {
        if ($id) {
            $media = $this->getBusinessProfilesManager()->getBusinessGalleryMediaById($id);

            if ($media) {
                $html = $this->renderView('widgets/picture_macros_wrapper.html.twig', [
                    'object' => $media,
                    'format' => 'preview',
                ]);

                return new JsonResponse([
                    'success' => true,
                    'data' => [
                        'html' => $html,
                    ],
                ]);
            }
        }

        return $this->getFailureResponse(self::ADMIN_MEDIA_PREVIEW_NO_FILE);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminUploadAction(Request $request)
    {
        if (!$this->isGranted(['ROLE_ADMINISTRATOR', 'ROLE_CONTENT_MANAGER'])) {
            $this->throwBusinessNotFoundException();
        }

        $file = $request->files->get('file', null);
        $error = '';

        if (!$file) {
            $error = self::ADMIN_MEDIA_UPLOAD_NO_FILE;
        } elseif (!$file->getSize() > Media::IMAGE_MAX_SIZE) {
            $error = self::ADMIN_MEDIA_UPLOAD_MAX_SIZE;
        } elseif (!in_array($file->getMimeType(), AdminHelper::getFormImageFileConstrain()['mimeTypes'])) {
            $error = self::ADMIN_MEDIA_UPLOAD_INVALID_EXTENSION;
        } else {
            $media = $this->getBusinessGalleryManager()->createNewAdminMediaEntryFromUploadedFile(
                $file,
                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES
            );

            $html = $this->renderView('widgets/picture_macros_wrapper.html.twig', [
               'object' => $media,
               'format' => 'preview',
            ]);

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'html' => $html,
                    'id'   => $media->getId(),
                    'name' => $media->getName(),
                ],
            ]);
        }

        return $this->getFailureResponse($error);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NoResultException
     * @throws \Exception
     */
    public function uploadRemoteImageAction(Request $request)
    {
        $businessProfileId = (int)$request->get(self::BUSINESS_PROFILE_ID_PARAMNAME, 0);

        $business = $this->getBusinessProfilesManager()->find($businessProfileId);

        if ($business === null) {
            $this->throwBusinessNotFoundException();
        }

        $error = $this->getBusinessGalleryManager()->createNewEntryFromRemoteFile(
            $business,
            $request->get('context', OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES),
            $request->get('url')
        );

        if (!$error) {
            $imagesForm = $this->getImagesForm($business);

            return $this->render(':redesign/blocks/businessProfile/subTabs/profile/gallery:images.html.twig', [
                'images'     => $imagesForm['images']->createView(),
                'logo'       => $imagesForm['logo']->createView(),
                'background' => $imagesForm['background']->createView(),
            ]);
        } else {
            return $this->getFailureResponse(
                $this->getTranslator()->trans($error['message'], $error['params'], 'validators'),
                []
            );
        }
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
     * @return BusinessGalleryManager
     */
    private function getBusinessGalleryManager() : BusinessGalleryManager
    {
        return $this->get('domain_business.manager.business_gallery');
    }

    /**
     * @return \Domain\BusinessBundle\Manager\BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return \Symfony\Component\Form\FormInterface[]
     */
    private function getImagesForm(BusinessProfile $businessProfile)
    {
        $form = $this->createForm(BusinessProfileFormType::class, $businessProfile);

        $result = [
            'images'     => $form->get('images'),
            'logo'       => $form->get('logo'),
            'background' => $form->get('background'),
        ];

        return $result;
    }
}
