<?php

namespace Domain\BannerBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="type_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_type_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class TypeTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BannerBundle\Entity\Type", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
