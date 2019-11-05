<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
    public static function extractEntitiesId(array $searchResults)
    {
        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, $searchResults);

        return $ids;
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

        $businessProfileName = $businessProfile->getName();
        $locale = LocaleHelper::getLocale($locale);

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

        $name = $businessProfile->getName();
        $locale = LocaleHelper::getLocale($locale);
        $catalogLocalityName = $businessProfile->getCatalogLocality()->getTranslation(
            Locality::LOCALITY_FIELD_NAME,
            $locale
        );

        $translator = $container->get('translator');

        $workingHours = self::getWorkingHoursAsText($businessProfile, $locale, $translator);

        $seoDescription = $translator->trans(
            'business_profile.seoDescription.main',
            [
                'name'     => $name,
                'location' => $catalogLocalityName,
            ],
            'messages',
            $locale
        );

        if ($workingHours and mb_strlen($seoDescription) < $descriptionMaxLength) {


            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.open',
                [
                    'hours' => $workingHours,
                ],
                'messages',
                $locale
            );
        }

        $hasLink = false;

        if ($businessProfile->getWebsiteLink() and mb_strlen($seoDescription) < $descriptionMaxLength) {
            $seoDescription .= ' ' . $translator->trans(
                'business_profile.seoDescription.link',
                [
                    'link' => $businessProfile->getWebsiteLink(),
                ],
                'messages',
                $locale
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
                $locale
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

    /**
     * @param BusinessProfile       $businessProfile
     * @param string                $locale
     * @param TranslatorInterface   $translator
     *
     * @return string
     */
    public static function getWorkingHoursAsText(BusinessProfile $businessProfile, $locale, $translator)
    {
        $workingHours = json_decode(DayOfWeekModel::getBusinessProfileWorkingHoursJson($businessProfile));

        $locale = LocaleHelper::getLocale($locale);
        $dayText = [];

        $openAllTimeText = $translator->trans(
            'business.working.hours.open_all_time',
            [],
            'messages',
            $locale
        );

        foreach ($workingHours as $day => $workingHour) {
            if ($workingHour) {
                $text = $translator->trans($day) . ' ';
                $hoursText = [];

                foreach ($workingHour as $item) {
                    $hours = '';

                    if (!$item->openAllTime) {
                        $hours .= $item->timeStart . '-' . $item->timeEnd;
                    } else {
                        $hours .= $openAllTimeText;
                    }

                    if (!empty($item->comment->$locale)) {
                        $hours .= ' - ' . $item->comment->$locale;
                    }

                    $hoursText[] = $hours;
                }

                $dayText[] = $text . implode(', ', $hoursText);
            }
        }

        return implode('; ', $dayText);
    }

    /**
     * @param BusinessProfile[] $businesses
     * @param array             $trackingParams
     *
     * @return array
     */
    public static function getTrackingVisitParamsData($businesses, $trackingParams = [])
    {
        $trackingParams = self::getTrackingParamsData(
            BusinessOverviewModel::TYPE_CODE_VIEW,
            $businesses,
            $trackingParams
        );

        return $trackingParams;
    }

    /**
     * @param BusinessProfile[] $businesses
     * @param array             $trackingParams
     *
     * @return array
     */
    public static function getTrackingImpressionParamsData($businesses, $trackingParams = [])
    {
        $trackingParams = self::getTrackingParamsData(
            BusinessOverviewModel::TYPE_CODE_IMPRESSION,
            $businesses,
            $trackingParams
        );

        return $trackingParams;
    }

    /**
     * @param string            $search
     * @param BusinessProfile[] $businesses
     * @param array             $trackingParams
     *
     * @return array
     */
    public static function getTrackingKeywordsParamsData($search, $businesses, $trackingParams = [])
    {
        $keywords = mb_strtolower($search);

        if ($keywords and $businesses) {
            $trackingParams[BusinessOverviewModel::TYPE_CODE_KEYWORD] = self::getTrackingParamsData(
                $keywords,
                $businesses
            );
        }

        return $trackingParams;
    }

    /**
     * @param string     $type
     * @param Category[] $categories
     * @param Locality[] $localities
     * @param array      $trackingParams
     *
     * @return array
     */
    public static function getTrackingCategoriesParamsData($type, $categories, $localities, $trackingParams = [])
    {
        if ($categories and $localities and $type) {
            $trackingParams[$type] = [];

            foreach ($localities as $locality) {
                $trackingParams[$type][] = self::getTrackingParamsData(
                    $locality->getId(),
                    $categories
                );
            }
        }

        return $trackingParams;
    }

    /**
     * @param string $action
     * @param array  $entities
     * @param array  $trackingParams
     *
     * @return array
     */
    public static function getTrackingParamsData($action, $entities, $trackingParams = [])
    {
        if ($entities) {
            $trackingParams[$action] = BusinessProfileUtil::extractEntitiesId($entities);
        }

        return $trackingParams;
    }
}
