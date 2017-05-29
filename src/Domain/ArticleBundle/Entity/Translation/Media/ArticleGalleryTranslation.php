<?php

namespace Domain\ArticleBundle\Entity\Translation\Media;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="article_gallery_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_article_gallery_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ArticleGalleryTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\ArticleBundle\Entity\Media\ArticleGallery", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}