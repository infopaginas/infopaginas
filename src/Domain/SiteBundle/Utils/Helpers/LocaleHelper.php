<?php

namespace Domain\SiteBundle\Utils\Helpers;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
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
     * @param string $locale
     *
     * @return string
     */
    public static function getLocale($locale)
    {
        if (array_key_exists($locale, self::getLocaleList())) {
            $currentLocale = $locale;
        } else {
            $currentLocale = self::DEFAULT_LOCALE;
        }

        return $currentLocale;
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
     * @param OxaPersonalTranslatableInterface $entity
     * @param string $property
     * @param array $data
     *
     * @return OxaPersonalTranslatableInterface
     */
    public static function handleTranslations(OxaPersonalTranslatableInterface $entity, $property, $data)
    {
        if (property_exists($entity, $property)) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $defaultValue = null;

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $propertyLocale = $property . LocaleHelper::getLangPostfix($locale);

                if (!empty($data[$propertyLocale])) {
                    $value = trim($data[$propertyLocale]);

                    if (!$defaultValue) {
                        $defaultValue = $value;
                    }

                    self::addEntityTranslation($entity, $property, $value, $locale);
                } else {
                    $value = null;

                    self::removeEntityTranslation($entity, $property, $locale);
                }

                if (property_exists($entity, $propertyLocale)) {
                    $accessor->setValue($entity, $propertyLocale, $value);
                }
            }

            $accessor->setValue($entity, $property, $defaultValue);
        }

        return $entity;
    }

    /**
     * @param OxaPersonalTranslatableInterface $entity
     * @param string $property
     * @param mixed $data
     * @param string $locale
     *
     * @return OxaPersonalTranslatableInterface
     */
    public static function addEntityTranslation(OxaPersonalTranslatableInterface $entity, $property, $data, $locale)
    {
        $translation = $entity->getTranslationItem($property, $locale);

        if ($translation) {
            $translation->setContent($data);
        } else {
            $translationClass = $entity->getTranslationClass();

            if (class_exists($translationClass)) {
                $translation = new $translationClass(
                    $locale,
                    $property,
                    $data
                );

                $entity->addTranslation($translation);
            }
        }

        return $entity;
    }

    /**
     * @param OxaPersonalTranslatableInterface $entity
     * @param string $property
     * @param string $locale
     *
     * @return OxaPersonalTranslatableInterface
     */
    public static function removeEntityTranslation(OxaPersonalTranslatableInterface $entity, $property, $locale)
    {
        $translation = $entity->getTranslationItem($property, $locale);

        if ($translation) {
            $entity->removeTranslation($translation);
        }

        return $entity;
    }

    /**
     * @param $locale
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getLanguageCodeForSCAYT($locale)
    {
        switch ($locale) {
            case self::LOCALE_EN:
                $languageCode = 'en_US';
                break;
            case self::LOCALE_ES:
                $languageCode = 'es_ES';
                break;
            default:
                throw new \InvalidArgumentException('Undefined locale "' . $locale . '"');
        }

        return $languageCode;
    }
}
