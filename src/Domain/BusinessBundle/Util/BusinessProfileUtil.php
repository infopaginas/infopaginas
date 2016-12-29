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
        $locale = false
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName    = $seoSettings['company_name'];
        $titleMaxLength = $seoSettings['title_max_length'];

        $businessProfileMaxLength = $seoSettings['business_name_length'];
        $localityMaxLength = $seoSettings['locality_length'];
        $brandMaxLength = $seoSettings['brand_length'];

        if ($locale) {
            $catalogLocalityName = $businessProfile->getCatalogLocality()->getTranslation('name', strtolower($locale));
            $businessProfileName = $businessProfile
                ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,  strtolower($locale));
        } else {
            $catalogLocalityName = $businessProfile->getCatalogLocality()->getName();
            $businessProfileName = $businessProfile->getName();
        }

        $translator = $container->get('translator');

        $seoTitle = $translator->trans(
            'business_profile.seoTitle',
            [
                'name'     => mb_substr($businessProfileName, 0 , $businessProfileMaxLength),
                'location' => mb_substr($catalogLocalityName, 0, $localityMaxLength),
                'company'  => mb_substr($companyName, 0, $brandMaxLength),
            ],
            'messages',
            $businessProfile->getLocale()
        );

        $seoTitle = mb_substr($seoTitle, 0, $titleMaxLength);

        return $seoTitle;
    }

    public static function seoDescriptionBuilder(
        BusinessProfile $businessProfile,
        ContainerInterface $container,
        $locale = false
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $descriptionMaxLength = $seoSettings['description_max_length'];

        if ($locale) {
            $description = $businessProfile
                ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,  strtolower($locale));

            $catalogLocalityName = $businessProfile->getCatalogLocality()->getTranslation(
                BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,
                strtolower($locale)
            );
            $workingHours = $businessProfile->getTranslation(
                BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS,
                $locale
            );
        } else {
            $description = $businessProfile->getDescription();
            $catalogLocalityName = $businessProfile->getCatalogLocality()->getName();
            $workingHours = $businessProfile->getWorkingHours();
        }

        $translator = $container->get('translator');

        $seoDescription = $translator->trans(
            'business_profile.seoDescription.main',
            [
                'name'     => $description,
                'location' => $catalogLocalityName,
            ],
            'messages',
            $businessProfile->getLocale()
        );

        if ($businessProfile->getWorkingHours() and mb_strlen($seoDescription) < $descriptionMaxLength) {


            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.open',
                [
                    'hours' => $workingHours,
                ],
                'messages',
                $businessProfile->getLocale()
            );
        }

        if ($businessProfile->getWebsite() and mb_strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.link',
                [
                    'link' => $businessProfile->getWebsiteLink(),
                ],
                'messages',
                $businessProfile->getLocale()
            );
        }

        if (!$businessProfile->getPhones()->isEmpty() and mb_strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.phone',
                [
                    'phone' => $businessProfile->getPhones()->first()->getPhone(),
                ],
                'messages',
                $businessProfile->getLocale()
            );
        }

        $seoDescription = mb_substr($seoDescription, 0, $descriptionMaxLength);

        return $seoDescription;
    }
}
