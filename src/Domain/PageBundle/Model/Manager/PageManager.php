<?php

namespace Domain\PageBundle\Model\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Domain\PageBundle\Entity\Page;

/**
 * Class PageManager
 * Page management entry point
 *
 * @package Domain\PageBundle\Manager
 */
class PageManager extends Manager
{
    public function getPageByCode($code)
    {
        return $this->getRepository()->findOneBy(['code' => $code]);
    }

    public function getPage()
    {
        return new Page();
    }
}
