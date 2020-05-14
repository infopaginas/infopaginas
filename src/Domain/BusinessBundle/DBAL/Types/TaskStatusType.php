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
 * Class TaskStatusType
 * Provide 'status' field enum collection for Task Entity
 *
 * @package Domain\BusinessBundle\DBAL\Types
 */
final class TaskStatusType extends AbstractEnumType
{
    public const TASK_STATUS_OPEN      = 'OPEN';
    public const TASK_STATUS_CLOSED    = 'CLOSED';
    public const TASK_STATUS_REJECTED  = 'REJECTED';

    protected static $choices = [
        self::TASK_STATUS_OPEN     => 'Incomplete',
        self::TASK_STATUS_CLOSED   => 'Approved',
        self::TASK_STATUS_REJECTED => 'Rejected',
    ];
}
