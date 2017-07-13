<?php

namespace Domain\PageBundle\Model\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Domain\PageBundle\Entity\Page;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageManager
 * Page management entry point
 *
 * @package Domain\PageBundle\Manager
 */
class PageManager extends Manager
{
    /**
     * @param int $code
     *
     * @return Page|null
     */
    public function getPageByCode($code)
    {
        return $this->getRepository()->findOneBy(['code' => $code]);
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return new Page();
    }

    /**
     * @param Page $entity
     * @param ContainerInterface $container
     *
     * @return Page
     */
    public function setPageSeoData(Page $entity, ContainerInterface $container)
    {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName          = $seoSettings['company_name'];
        $titleMaxLength       = $seoSettings['title_max_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];

        $seoTitle = $entity->getTitle() . ' | ' . $companyName;
        $seoDescription = $entity->getDescription();

        $seoTitle       = mb_substr($seoTitle, 0, $titleMaxLength);
        $seoDescription = mb_substr($seoDescription, 0, $descriptionMaxLength);

        $entity->setSeoTitle($seoTitle);
        $entity->setSeoDescription($seoDescription);

        return $entity;
    }
}
