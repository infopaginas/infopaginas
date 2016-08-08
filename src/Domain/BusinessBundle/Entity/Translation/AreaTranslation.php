<?php

namespace Domain\BusinessBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="area_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_area_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 * @UniqueEntity("name")
 */
class AreaTranslation extends AbstractPersonalTranslation implements CopyableEntityInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Area", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }
}
