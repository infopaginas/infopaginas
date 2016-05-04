<?php

namespace Application\Sonata\UserBundle\Entity;

use Application\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Application\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Application\Sonata\AdminBundle\Util\Traits\AvailableUserEntityTrait;
use Application\Sonata\AdminBundle\Util\Traits\DeleteableUserEntityTrait;
use Application\Sonata\AdminBundle\Util\Traits\UserCUableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\UserRepository")
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
