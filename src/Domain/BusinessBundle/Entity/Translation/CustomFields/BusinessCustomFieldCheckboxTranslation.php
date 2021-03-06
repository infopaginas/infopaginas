<?php

namespace Domain\BusinessBundle\Entity\Translation\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="business_custom_field_checkbox_translation",
 *     uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *         name="lookup_unique_business_custom_field_checkbox_translation_idx",
 *         columns={"locale", "object_id", "field"}
 *     )}
 * )
 */
class BusinessCustomFieldCheckboxTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckbox",
     *     inversedBy="translations"
     * )
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
