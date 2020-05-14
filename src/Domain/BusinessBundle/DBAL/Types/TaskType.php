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
    public const TASK_PROFILE_CREATE = 'PROFILE_CREATE';
    public const TASK_PROFILE_UPDATE = 'PROFILE_UPDATE';
    public const TASK_PROFILE_CLOSE  = 'PROFILE_CLOSE';
    public const TASK_REVIEW_APPROVE = 'REVIEW_APPROVE';
    public const TASK_PROFILE_CLAIM  = 'PROFILE_CLAIM';

    protected static $choices = [
        self::TASK_PROFILE_CREATE => 'Create Profile',
        self::TASK_PROFILE_UPDATE => 'Update Profile',
        self::TASK_PROFILE_CLOSE  => 'Close Profile',
        self::TASK_REVIEW_APPROVE => 'Business Review',
        self::TASK_PROFILE_CLAIM  => 'Business Claim',
    ];
}
