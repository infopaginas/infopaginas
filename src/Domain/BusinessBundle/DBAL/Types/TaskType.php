<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 14.05.16
 * Time: 12:33
 */

namespace Domain\BusinessBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class TaskType
 * Provide 'type' field enum collection for Task Entity
 *
 * @package Domain\BusinessBundle\DBAL\Types
 */
final class TaskType extends AbstractEnumType
{
    const TASK_PROFILE_CREATE = 'PROFILE_CREATE';
    const TASK_PROFILE_UPDATE = 'PROFILE_UPDATE';
    const TASK_PROFILE_CLOSE  = 'PROFILE_CLOSE';
    const TASK_REVIEW_APPROVE = 'REVIEW_APPROVE';

    protected static $choices = [
        self::TASK_PROFILE_CREATE => 'Create Profile',
        self::TASK_PROFILE_UPDATE => 'Update Profile',
        self::TASK_PROFILE_CLOSE  => 'Close Profile',
        self::TASK_REVIEW_APPROVE => 'Business Review',
    ];
}
