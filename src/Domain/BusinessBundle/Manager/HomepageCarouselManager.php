<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\HomepageCarousel;
use Domain\BusinessBundle\Repository\HomepageCarouselRepository;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

/**
 * @method HomepageCarouselRepository getRepository()
 */
class HomepageCarouselManager extends Manager
{
    public function getCarouselBusinessesSortedByPosition()
    {
        return $this->getRepository()->findBy([], ['position' => 'ASC']);
    }

    /**
     * @return HomepageCarousel[]
     */
    public function getCarouselBusinessesSortedByRandom()
    {
        $items = $this->getRepository()->findAll();

        shuffle($items);

        return $items;
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
