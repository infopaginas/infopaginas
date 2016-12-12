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

    public static function seoTitleBuilder(
        BusinessProfile $businessProfile,
        ContainerInterface $container,
        $locale = BusinessProfile::DEFAULT_LOCALE
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName    = $seoSettings['company_name'];
        $titleMaxLength = $seoSettings['title_max_length'];

        $catalogLocalityName = $businessProfile->getCatalogLocality()->getTranslation('name', strtolower($locale));
        $businessProfileName = $businessProfile
            ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,  strtolower($locale));

        $seoTitle = $businessProfileName . ' - ' . $catalogLocalityName . ' | ' . $companyName;
        $seoTitle = substr($seoTitle, 0, $titleMaxLength);

        return $seoTitle;
    }

    public static function seoDescriptionBuilder(
        BusinessProfile $businessProfile,
        ContainerInterface $container,
        $locale = BusinessProfile::DEFAULT_LOCALE
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $descriptionMaxLength = $seoSettings['description_max_length'];

        $description = $businessProfile
            ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,  strtolower($locale));

        $seoDescription = substr($description, 0, $descriptionMaxLength);

        return $seoDescription;
    }
}
