<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Manager\BusinessGalleryManager;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImagesController
 * @package Domain\BusinessBundle\Controller
 */
class ImagesController extends Controller
{
    use JsonResponseBuilderTrait;

    const BUSINESS_PROFILE_ID_PARAMNAME = 'businessProfileId';

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
            'background' => $imagesForm['logo']->createView(),
        ]);
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

        $business = $this->getBusinessGalleryManager()->createNewEntryFromRemoteFile(
            $business,
            $request->get('context', OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES),
            $request->get('url')
        );

        if ($business) {
            $imagesForm = $this->getImagesForm($business);

            return $this->render(':redesign/blocks/businessProfile/subTabs/profile/gallery:images.html.twig', [
                'images'     => $imagesForm->createView(),
                'logo'       => $imagesForm['logo']->createView(),
                'background' => $imagesForm['logo']->createView(),
            ]);
        } else {
            return $this->getFailureResponse(
                $this->getTranslator()->trans('business_profile.images.invalid_url', [], 'validators'),
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
        $form = $this->createForm(new BusinessProfileFormType(), $businessProfile);

        $result = [
            'images'     => $form->get('images'),
            'logo'       => $form->get('logo'),
            'background' => $form->get('background'),
        ];

        return $result;
    }
}
