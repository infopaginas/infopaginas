<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

trait OxaPersonalTranslatable
{
    use PersonalTranslatable;

    /**
     * @var ArrayCollection $translations
     */
    protected $translations;

    /**
     * Get translation, return original(english) version, if translation is empty
     *
     * @param $field
     * @param $locale
     * @return string
     */
    public function getTranslation($field, $locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if (strcmp($translation->getField(), $field) === 0 && strcmp($translation->getLocale(), $locale) === 0) {
                $result = $translation->getContent();
            }
        }

        if (!isset($result)) {
            $result = $this->{'get'.ucfirst($field)}();
        }

        return $result;
    }

    /**
     * @param $field
     * @param $locale
     *
     * @return mixed
     */
    public function getTranslationItem($field, $locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if (strcmp($translation->getField(), $field) === 0 && strcmp($translation->getLocale(), $locale) === 0) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * Remove translation
     *
     * @param AbstractPersonalTranslation $translation
     */
    public function removeTranslation(AbstractPersonalTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
