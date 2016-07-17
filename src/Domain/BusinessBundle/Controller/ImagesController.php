<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Form\Type\BusinessGalleryType;
use Domain\BusinessBundle\Form\Type\FreeBusinessProfileFormType;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImagesController
 * @package Domain\BusinessBundle\Controller
 */
class ImagesController extends Controller
{
    const BUSINESS_PROFILE_ID_PARAMNAME = 'businessProfileId';

    public function uploadAction(Request $request)
    {
        $businessProfileId = (int)$request->get(self::BUSINESS_PROFILE_ID_PARAMNAME, 0);

        $business = $this->getBusinessProfilesManager()->find($businessProfileId);

        if (!$business) {
            throw new NoResultException('Business isnt found');
        }

        $fileBag = $request->files;

        $manager = $this->get('sonata.media.manager.media');

        $images = [];

        /** @var UploadedFile $file */
        foreach ($fileBag->get('files') as $file) {
            $media = new Media();
            $media->setBinaryContent($file);
            $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES);
            $media->setProviderName(OxaMediaInterface::PROVIDER_IMAGE);
            $manager->save($media, false);

            array_push($images, $media);
        }

        $this->getDoctrine()->getManager()->flush();

        foreach ($images as $image) {
            $businessGallery = new BusinessGallery();
            $businessGallery->setMedia($image);

            $business->addImage($businessGallery);
        }

        $form = $this->createForm(new FreeBusinessProfileFormType(), $business);

        return $this->render('DomainBusinessBundle:Images/blocks:gallery.html.twig', [
            'images' => $form->get('images')->createView(),
        ]);
    }

    private function getBusinessProfilesManager()
    {
        return $this->get('domain_business.manager.business_profile');
    }
}
