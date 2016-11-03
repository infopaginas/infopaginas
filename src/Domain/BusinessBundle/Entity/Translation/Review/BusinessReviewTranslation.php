<?php

namespace Domain\BusinessBundle\Entity\Translation\Review;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="business_review_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_business_review_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class BusinessReviewTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Review\BusinessReview", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
