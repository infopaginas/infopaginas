<?php

namespace Domain\SiteBundle\Utils\Helpers;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class LocaleHelper
{
    const LOCALE_EN = 'en';
    const LOCALE_ES = 'es';

    const DEFAULT_LOCALE = self::LOCALE_EN;
    const SLUG_LOCALE    = self::LOCALE_ES;

    /**
     * @param string $locale
     *
     * @return string
     */
    public static function getLangPostfix($locale)
    {
        if (array_key_exists($locale, self::getLocaleList())) {
            $currentLocale = $locale;
        } else {
            $currentLocale = self::DEFAULT_LOCALE;
        }

        return ucfirst($currentLocale);
    }

    /**
     * @return array
     */
    public static function getLocaleList()
    {
        return [
            self::LOCALE_EN => 'English',
            self::LOCALE_ES => 'Spanish',
        ];
    }

    /**
     * @param BusinessProfile $business
     *
     * @return BusinessProfile
     */
    public static function handleSeoBlockUpdate(BusinessProfile $business, ContainerInterface $container)
    {
        $seoTitleData = [];
        $seoDescData  = [];

        foreach (LocaleHelper::getLocaleList() as $locale => $name) {
            $localePostfix  = LocaleHelper::getLangPostfix($locale);

            $seoTitle       = BusinessProfileUtil::seoTitleBuilder($business, $container, $locale);
            $seoDescription = BusinessProfileUtil::seoDescriptionBuilder($business, $container, $locale);

            $seoTitleData[BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . $localePostfix] = $seoTitle;
            $seoDescData[BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . $localePostfix] = $seoDescription;
        }

        self::handleTranslations($business, BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE, $seoTitleData);
        self::handleTranslations($business, BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION, $seoDescData);

        return $business;
    }

    /**
     * @param BusinessProfile   $business
     * @param string            $property
     * @param array             $data
     *
     * @return BusinessProfile
     */
    public static function handleTranslations(BusinessProfile $business, $property, $data)
    {
        if (property_exists($business, $property)) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $defaultValue = null;

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $propertyLocale = $property . LocaleHelper::getLangPostfix($locale);

                if (!empty($data[$propertyLocale])) {
                    $value = trim($data[$propertyLocale]);

                    if (!$defaultValue) {
                        $defaultValue = $value;
                    }

                    self::addBusinessTranslation($business, $property, $value, $locale);
                } else {
                    $value = null;

                     self::removeBusinessTranslation($business, $property, $locale);
                }

                if (property_exists($business, $propertyLocale)) {
                    $accessor->setValue($business, $propertyLocale, $value);
                }
            }

            $accessor->setValue($business, $property, $defaultValue);
        }

        return $business;
    }

    /**
     * @param BusinessProfile   $business
     * @param string            $property
     * @param array             $data
     * @param string            $locale
     *
     * @return BusinessProfile
     */
    public static function addBusinessTranslation(BusinessProfile $business, $property, $data, $locale)
    {
        $translation = $business->getTranslationItem($property, $locale);

        if ($translation) {
            $translation->setContent($data);
        } else {
            $translation = new BusinessProfileTranslation(
                $locale,
                $property,
                $data
            );

            $business->addTranslation($translation);
        }

        return $business;
    }

    /**
     * @param BusinessProfile   $business
     * @param string            $property
     * @param string            $locale
     *
     * @return BusinessProfile
     */
    public static function removeBusinessTranslation(BusinessProfile $business, $property, $locale)
    {
        $translation = $business->getTranslationItem($property, $locale);

        if ($translation) {
            $business->removeTranslation($translation);
        }

        return $business;
    }
}
