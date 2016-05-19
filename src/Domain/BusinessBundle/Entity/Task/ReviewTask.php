<?php

namespace Domain\BusinessBundle\Entity\Task;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\TaskInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\UserBundle\Entity\User as User;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;

/**
 * ReviewTask
 *
 * @ORM\Table(name="review_task")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ReviewTaskRepository")
 */
class ReviewTask extends Task
{
    

}
