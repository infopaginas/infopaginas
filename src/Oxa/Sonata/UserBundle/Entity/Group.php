<?php

namespace Oxa\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="fos_user_group")
 * @ORM\Entity(repositoryClass="Oxa\Sonata\UserBundle\Entity\Repository\GroupRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Oxa\Sonata\UserBundle\Entity\Translation\GroupTranslation")
 * @UniqueEntity("code")
 */
class Group extends BaseGroup implements DefaultEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    // codes are also used as priority in admin panel to know if an user is able to edit another user
    const CODE_ADMINISTRATOR    = 1; // Admin portal user
    const CODE_CONTENT_MANAGER  = 2; // Admin portal user
    const CODE_SALES_MANAGER    = 3; // Admin portal user
    const CODE_MERCHANT         = 4; // Customer, manages Businesses
    const CODE_CONSUMER         = 5; // Visitor, has site profile

    public static $groupRoles = [
        self::CODE_ADMINISTRATOR    => 'ROLE_ADMINISTRATOR',
        self::CODE_CONTENT_MANAGER  => 'ROLE_CONTENT_MANAGER',
        self::CODE_SALES_MANAGER    => 'ROLE_SALES_MANAGER',
        self::CODE_MERCHANT         => 'ROLE_MERCHANT',
        self::CODE_CONSUMER         => 'ROLE_CONSUMER',
    ];

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="code", type="integer", nullable=false, options={"default" = 1})
     */
    protected $code;

    /**
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var User
     *
     * @ORM\OneToMany(
     *     targetEntity="Oxa\Sonata\UserBundle\Entity\User",
     *     mappedBy="role",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @ORM\OrderBy({"createdAt" = "asc"})
     */
    protected $roleUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Oxa\Sonata\UserBundle\Entity\Translation\GroupTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Group constructor.
     * @param $name
     * @param array $roles
     */
    public function __construct($name, $roles = array())
    {
        parent::__construct($name, $roles = array());
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Set code
     *
     * @param integer $code
     *
     * @return Group
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add roleUser
     *
     * @param \Oxa\Sonata\UserBundle\Entity\User $roleUser
     *
     * @return Group
     */
    public function addRoleUser(\Oxa\Sonata\UserBundle\Entity\User $roleUser)
    {
        $this->roleUsers[] = $roleUser;

        return $this;
    }

    /**
     * Remove roleUser
     *
     * @param \Oxa\Sonata\UserBundle\Entity\User $roleUser
     */
    public function removeRoleUser(\Oxa\Sonata\UserBundle\Entity\User $roleUser)
    {
        $this->roleUsers->removeElement($roleUser);
    }

    /**
     * Get roleUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleUsers()
    {
        return $this->roleUsers;
    }

    /**
     * Remove translation
     *
     * @param \Oxa\Sonata\UserBundle\Entity\Translation\GroupTranslation $translation
     */
    public function removeTranslation(\Oxa\Sonata\UserBundle\Entity\Translation\GroupTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
