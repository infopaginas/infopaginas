<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 16.05.16
 * Time: 15:21
 */

namespace Domain\BusinessBundle\Model\Task;

use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Task;

/**
 * Class AbstractTask
 * Profile FactoryMethod for Tasks generation
 *
 * @package Domain\BusinessBundle\Model\Task
 */
abstract class TasksFactory extends Task
{
    /**
     * Create Task object here
     *
     * @access public
     * @param string $type
     * @param BusinessProfile $businessProfile
     * @param null $businessReview
     * @return Task
     */
    public static function create(string $type, BusinessProfile $businessProfile, $businessReview = null) : Task
    {
        $task = new Task();
        $task->setType($type);
        $task->setBusinessProfile($businessProfile);
        $task->setBusinessProfileUID($businessProfile->getUid());

        if ($businessProfile->getLocale() !== BusinessProfile::DEFAULT_LOCALE) {
            $task->setLocale($businessProfile->getLocale());
        }

        if ($businessReview !== null) {
            $task->setReview($businessReview);
        }

        return $task;
    }
}
