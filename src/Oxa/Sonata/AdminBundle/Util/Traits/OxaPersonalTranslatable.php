<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/12/16
 * Time: 7:09 PM
 */

namespace Oxa\Sonata\AdminBundle\Util\Traits;

use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;

trait OxaPersonalTranslatable
{
    use PersonalTranslatable;

    /**
     * Get translation, return original(english) version, if translation is empty
     *
     * @param $field
     * @param $locale
     */
    public function getTranslation($field, $locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if (strcmp($translation->getField(), $field) === 0 && strcmp($translation->getLocale(), $locale) === 0) {
                $result = $translation->getContent();
            }
        }

        if (!isset($result)) {
            return $this->{'get'.ucfirst($field)}();
        }

        return;
    }
}
