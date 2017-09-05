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
    const SEO_TITLE_SEPARATOR = ' | ';

    protected $seoParams = [];

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
     * @param Page  $page
     * @param array $data
     *
     * @return array
     */
    public function getPageSeoData($page, $data = [])
    {
        $code = $page->getCode();

        $seoTitle = $this->replacePlaceholders(
            $page->getSeoTitle(),
            Page::getPageSeoHintByCode($code)['placeholders'],
            $data
        );
        $seoDescription = $this->replacePlaceholders(
            $page->getSeoDescription(),
            Page::getPageSeoHintByCode($code)['placeholders'],
            $data
        );

        $seoData = [
            'seoTitle'       => $this->getSeoTitleWithBrand($seoTitle),
            'seoDescription' => $seoDescription,
        ];

        return $seoData;
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

    /**
     * @param string    $string
     * @param array     $search
     * @param array     $replace
     *
     * @return string
     */
    public function replacePlaceholders($string, $search, $replace)
    {
        $result = $string;

        foreach ($search as $placeholder) {
            if (!empty($replace[$placeholder])) {
                $value = $replace[$placeholder];
            } else {
                $value = '';
            }

            $result = str_replace($placeholder, $value, $result);
        }

        return $result;
    }

    /**
     * @param array $params
     */
    public function setSeoParams($params)
    {
       $this->seoParams = $params;
    }

    /**
     * @param string $seoTitle
     *
     * @return string
     */
    protected function getSeoTitleWithBrand($seoTitle)
    {
        $title = '';
        $brand = $this->seoParams['company_name'];

        if ($seoTitle) {
            $title .= $seoTitle . self::SEO_TITLE_SEPARATOR;
        }

        $title .= $brand;

        return $title;
    }
}
