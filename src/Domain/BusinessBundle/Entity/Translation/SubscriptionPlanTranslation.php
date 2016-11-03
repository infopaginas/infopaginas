<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscription_plan_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_subscription_plan_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class SubscriptionPlanTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\SubscriptionPlan", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
