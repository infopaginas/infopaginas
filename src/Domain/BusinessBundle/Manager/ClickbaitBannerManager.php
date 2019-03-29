<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class ClickbaitBannerManager extends Manager
{
    public function getClickbaitBannerByLocality($locality)
    {
        return $locality ? $this->getRepository()->findOneBy(['locality' => $locality]) : null;
    }
}
