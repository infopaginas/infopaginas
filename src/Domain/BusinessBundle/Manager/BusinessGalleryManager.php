<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 17.07.16
 * Time: 16:09
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

/**
 * Class BusinessGalleryManager
 * @package Domain\BusinessBundle\Manager
 */
class BusinessGalleryManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * BusinessGalleryManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Used to restore images in case then task rejected
     *
     * @access public
     * @param BusinessProfile $businessProfile
     */
    public function restoreBusinessProfileImages(BusinessProfile $businessProfile)
    {
        $actualBusinessProfile = $businessProfile->getActualBusinessProfile();

        $images = $this->getRepository()->findBusinessProfileRemovedImages($actualBusinessProfile);

        foreach ($images as $image) {
            $image->setDeletedAt(null);
            $this->getEntityManager()->persist($image);
        }

        foreach ($businessProfile->getImages() as $image) {
            $actualBusinessProfile->addImage($image);
            $this->getEntityManager()->persist($actualBusinessProfile);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Go through business profile images and setup profile logo, if found
     *
     * @access public
     * @param BusinessProfile $businessProfile
     */
    public function setupBusinessProfileLogo(BusinessProfile $businessProfile)
    {
        foreach ($businessProfile->getImages() as $image) {
            if ($image->getType() === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                $businessProfile->setLogo($image->getMedia());
                $this->getEntityManager()->persist($businessProfile);
            }
        }
    }

    /**
     * @return BusinessGalleryRepository
     */
    private function getRepository() : BusinessGalleryRepository
    {
        return $this->getEntityManager()->getRepository('DomainBusinessBundle:Media\BusinessGallery');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }
}
