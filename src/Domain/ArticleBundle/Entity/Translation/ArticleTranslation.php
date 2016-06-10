<?php

namespace Domain\ArticleBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="article_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_article_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ArticleTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\ArticleBundle\Entity\Article", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
