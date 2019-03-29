<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="clickbait_banner_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_clickbait_banner_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ClickbaitTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\ClickbaitBanner", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
