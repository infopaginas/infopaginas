<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/11/16
 * Time: 11:35 AM
 */

namespace Oxa\Sonata\UserBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
//use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
//use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_group_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_fos_user_group_translation_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class GroupTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\Group", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}