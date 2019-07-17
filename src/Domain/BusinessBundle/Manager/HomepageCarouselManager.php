<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\HomepageCarousel;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class HomepageCarouselManager extends Manager
{
    public function getCarouselBusinessesSortedByPosition()
    {
        $carouselBusinesses = $this->em->getRepository(HomepageCarousel::class)->findBy(
            [],
            ['position' => 'ASC']
        );

        return $carouselBusinesses;
    }

    public function isShowCarousel($carouselBusinesses)
    {
        $showCarousel = false;

        foreach ($carouselBusinesses as $carouselBusiness) {
            if ($carouselBusiness->getBusinessProfile()->getIsActive() == true) {
                $showCarousel = true;

                break;
            }
        }

        return $showCarousel;
    }
}
