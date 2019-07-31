<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class HomepageCarouselManager extends Manager
{
    public function getCarouselBusinessesSortedByPosition()
    {
        return $this->getRepository()->findBy([], ['position' => 'ASC']);
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
