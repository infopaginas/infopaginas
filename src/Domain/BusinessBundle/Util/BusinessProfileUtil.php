<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessProfileUtil
{
    const SEO_CLASS_PREFIX_SEARCH     = 'search';
    const SEO_CLASS_PREFIX_SEARCH_MAP = 'search-map';

    const SEO_CLASS_PREFIX_COMPARE      = 'compare';
    const SEO_CLASS_PREFIX_COMPARE_MAP  = 'compare-map';

    const SEO_CLASS_PREFIX_CATALOG = 'catalog';
    const SEO_CLASS_PREFIX_PROFILE = 'profile';
    const SEO_CLASS_PREFIX_VIDEO   = 'video';

    const SEO_CLASS_BUSINESS_NAME = 'business-name';

    /**
     * @param array $searchResults
     *
     * @return array
     */
    public static function extractBusinessProfiles(array $searchResults)
    {
        return array_column($searchResults, 'id');
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param ContainerInterface $container
     * @param string|bool $locale
     *
     * @return string
     */
    public static function seoTitleBuilder(
        BusinessProfile $businessProfile,
        ContainerInterface $container,
        $locale = false
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName    = $seoSettings['company_name'];
        $titleMaxLength = $seoSettings['title_max_length'];

        $businessProfileMaxLength = $seoSettings['business_name_length'];
        $brandMaxLength = $seoSettings['brand_length'];

        if ($locale) {
            $businessProfileName = $businessProfile
                ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_NAME, strtolower($locale));
        } else {
            $businessProfileName = $businessProfile->getName();
            $locale = $businessProfile->getLocale();
        }

        $translator = $container->get('translator');

        $seoTitle = $translator->trans(
            'business_profile.seoTitle',
            [
                'name'     => mb_substr($businessProfileName, 0, $businessProfileMaxLength),
                'company'  => mb_substr($companyName, 0, $brandMaxLength),
            ],
            'messages',
            strtolower($locale)
        );

        $seoTitle = mb_substr($seoTitle, 0, $titleMaxLength);

        return $seoTitle;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param ContainerInterface $container
     * @param string|bool $locale
     *
     * @return string
     */
    public static function seoDescriptionBuilder(
        BusinessProfile $businessProfile,
        ContainerInterface $container,
        $locale = false
    ) {
        $seoSettings = $container->getParameter('seo_custom_settings');

        $descriptionMaxLength = $seoSettings['description_max_length'];

        if ($locale) {
            $name = $businessProfile
                ->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_NAME, strtolower($locale));

            $catalogLocalityName = $businessProfile->getCatalogLocality()->getTranslation(
                BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,
                strtolower($locale)
            );
            $workingHours = $businessProfile->getTranslation(
                BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS,
                $locale
            );
        } else {
            $name = $businessProfile->getName();
            $catalogLocalityName = $businessProfile->getCatalogLocality()->getName();
            $workingHours = $businessProfile->getWorkingHours();
            $locale = $businessProfile->getLocale();
        }

        $translator = $container->get('translator');

        $seoDescription = $translator->trans(
            'business_profile.seoDescription.main',
            [
                'name'     => $name,
                'location' => $catalogLocalityName,
            ],
            'messages',
            strtolower($locale)
        );

        if ($businessProfile->getWorkingHours() and mb_strlen($seoDescription) < $descriptionMaxLength) {


            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.open',
                [
                    'hours' => $workingHours,
                ],
                'messages',
                strtolower($locale)
            );
        }

        $hasLink = false;

        if ($businessProfile->getWebsite() and mb_strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.link',
                [
                    'link' => $businessProfile->getWebsiteLink(),
                ],
                'messages',
                strtolower($locale)
            );

            $hasLink = true;
        }

        if (!$businessProfile->getPhones()->isEmpty() and mb_strlen($seoDescription) < $descriptionMaxLength) {
            if ($hasLink) {
                $phoneTranslate = 'business_profile.seoDescription.phone_has_link';
            } else {
                $phoneTranslate = 'business_profile.seoDescription.phone';
            }

            $seoDescription .= ' ' . $translator->trans(
                $phoneTranslate,
                [
                    'phone' => $businessProfile->getPhones()->first()->getPhone(),
                ],
                'messages',
                strtolower($locale)
            );
        }

        $seoDescription = mb_substr($seoDescription, 0, $descriptionMaxLength);

        return $seoDescription;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public static function getSeoTags($type)
    {
        return [
            'name' => self::getBusinessProfileNameSeoClass($type),
        ];
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getBusinessProfileNameSeoClass($type)
    {
        return sprintf('%s-%s', $type, self::SEO_CLASS_BUSINESS_NAME);
    }
}
