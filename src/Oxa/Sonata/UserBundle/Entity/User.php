<?php

namespace Oxa\Sonata\UserBundle\Entity;

use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\AvailableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DeleteableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\UserCUableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Oxa\Sonata\UserBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="fos_user_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("username")
 */
class User extends BaseUser implements DefaultEntityInterface, CopyableEntityInterface
{
    use AvailableUserEntityTrait, DeleteableUserEntityTrait, UserCUableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getMarkCopyPropertyName()
    {
        return 'username';
    }
}
