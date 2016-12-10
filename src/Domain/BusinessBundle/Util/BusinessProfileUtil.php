<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessProfileUtil
{
    public static function extractBusinessProfiles(array $searchResults)
    {
        return array_column($searchResults, 0);
    }

    public static function seoTitleBuilder(BusinessProfile $businessProfile, ContainerInterface $container)
    {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName    = $seoSettings['company_name'];
        $titleMaxLength = $seoSettings['title_max_length'];

        $catalogLocalityName = $businessProfile->getCatalogLocality()->getName();

        $seoTitle = $businessProfile->getName() . ' - ' . $catalogLocalityName . ' | ' . $companyName;
        $seoTitle = substr($seoTitle, 0, $titleMaxLength);

        return $seoTitle;
    }

    public static function seoDescriptionBuilder(BusinessProfile $businessProfile, ContainerInterface $container)
    {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $descriptionMaxLength = $seoSettings['description_max_length'];

        $seoDescription = substr($businessProfile->getDescription(), 0, $descriptionMaxLength);

        return $seoDescription;
    }
}
