<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_method_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_payment_method_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class PaymentMethodTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
