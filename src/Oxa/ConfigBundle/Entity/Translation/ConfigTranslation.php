<?php

namespace Oxa\ConfigBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="config_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_config_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ConfigTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Oxa\ConfigBundle\Entity\Config", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
