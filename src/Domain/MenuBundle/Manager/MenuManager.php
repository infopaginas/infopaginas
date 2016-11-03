<?php

namespace Domain\MenuBundle\Manager;

use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class MenuManager extends Manager
{
    public function fetchAll()
    {
        return $this->em->getRepository('DomainMenuBundle:Menu')->getMenuItems();
    }
}
