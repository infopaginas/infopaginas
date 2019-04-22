<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class ClickbaitTitleManager extends Manager
{
    public function getClickbaitTitleByLocality($locality)
    {
        return $locality ? $this->getRepository()->findOneBy(['locality' => $locality]) : null;
    }
}
