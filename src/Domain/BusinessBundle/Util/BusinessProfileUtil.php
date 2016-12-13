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

        $translator = $container->get('translator');

        $seoTitle = $translator->trans(
            'business_profile.seoTitle',
            [
                'name'     => $businessProfile->getName(),
                'location' => $catalogLocalityName,
                'company'  => $companyName,
            ],
            'messages',
            $businessProfile->getLocale()
        );

        $seoTitle = mb_substr($seoTitle, 0, $titleMaxLength);

        return $seoTitle;
    }

    public static function seoDescriptionBuilder(BusinessProfile $businessProfile, ContainerInterface $container)
    {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $descriptionMaxLength = $seoSettings['description_max_length'];

        $catalogLocalityName = $businessProfile->getCatalogLocality()->getName();

        $translator = $container->get('translator');

        $seoDescription = $translator->trans(
            'business_profile.seoDescription.main',
            [
                'name'     => $businessProfile->getName(),
                'location' => $catalogLocalityName,
            ],
            'messages',
            $businessProfile->getLocale()
        );

        if ($businessProfile->getWorkingHours() and strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.open',
                [
                    'hours' => $businessProfile->getWorkingHours(),
                ],
                'messages',
                $businessProfile->getLocale()
            );
        }

        if ($businessProfile->getWebsite() and strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.link',
                [
                    'link' => $businessProfile->getWebsiteLink(),
                ],
                'messages',
                $businessProfile->getLocale()
            );
        }

        if (!$businessProfile->getPhones()->isEmpty() and strlen($seoDescription) < $descriptionMaxLength) {
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
