<?php

namespace Domain\PageBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_template_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_page_template_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class TemplateTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\PageBundle\Entity\Template", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
