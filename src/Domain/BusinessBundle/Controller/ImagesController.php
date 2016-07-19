<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Manager\BusinessGalleryManager;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImagesController
 * @package Domain\BusinessBundle\Controller
 */
class ImagesController extends Controller
{
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

        $business = $this->getBusinessGalleryManager()->fillBusinessGallery($business, $request->files);

        $form = $this->createForm(new BusinessProfileFormType(), $business);

        return $this->render('DomainBusinessBundle:Images/blocks:gallery.html.twig', [
            'images' => $form->get('images')->createView(),
        ]);
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
}
