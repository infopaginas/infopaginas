<?php

namespace Oxa\Sonata\AdminBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

/**
 * Should be implemented to entities that using translations
 *
 * Interface OxaPersonalTranslatableInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface OxaPersonalTranslatableInterface extends TranslatableInterface
{
    /**
     * @param $field
     * @param $locale
     * @return string
     */
    public function getTranslation($field, $locale);

    /**
     * @param $field
     * @param $locale
     *
     * @return mixed
     */
    public function getTranslationItem($field, $locale);

    /**
     * @return ArrayCollection|AbstractPersonalTranslation[]
     */
    public function getTranslations();

    /**
     * @return string
     */
    public function getTranslationClass(): string;

    /**
     * @param AbstractPersonalTranslation $translation
     *
     * @return $this
     */
    public function addTranslation(AbstractPersonalTranslation $translation);

    /**
     * Remove translation
     *
     * @param AbstractPersonalTranslation $translation
     */
    public function removeTranslation(AbstractPersonalTranslation $translation);
}
