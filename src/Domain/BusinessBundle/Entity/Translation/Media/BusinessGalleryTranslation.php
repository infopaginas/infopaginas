<?php

namespace Domain\BusinessBundle\Entity\Translation\Media;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="business_gallery_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_business_gallery_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class BusinessGalleryTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Media\BusinessGallery", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
