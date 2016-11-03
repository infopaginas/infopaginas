<?php

namespace Domain\BusinessBundle\Entity\Translation\Address;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="country_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_country_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class CountryTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Address\Country", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
