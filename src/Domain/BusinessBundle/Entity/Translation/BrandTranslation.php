<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="brand_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_brand_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class BrandTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Brand", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
