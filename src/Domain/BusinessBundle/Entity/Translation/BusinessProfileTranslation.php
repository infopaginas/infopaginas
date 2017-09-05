<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="business_profile_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_business_profile_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class BusinessProfileTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="translations",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @return string
     */
    public function __toString()
    {
        $data = [
            'id'        => $this->getId(),
            'locale'    => $this->getLocale(),
            'field'     => $this->getField(),
            'value'     => $this->getContent(),
        ];

        return json_encode($data) ?: '';
    }
}
