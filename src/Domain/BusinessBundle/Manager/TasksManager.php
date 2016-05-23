<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 16.05.16
 * Time: 15:41
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Task\Task;
use Domain\BusinessBundle\Model\Task\TasksFactory;
use Domain\BusinessBundle\Repository\TaskRepository;

/**
 * Class TasksManager
 * Tasks management entry point
 *
 * @package Domain\BusinessBundle\Manager
 */
class TasksManager
{
    const TASK_SUCCESSFULLY_CREATED_MESSAGE = 'Task was successfully created';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TasksManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->repository = $this->em->getRepository(TaskRepository::SLUG);
    }

    /**
     * Create new 'Create Business Profile' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function createNewProfileConfirmationRequest(BusinessProfile $businessProfile) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_CREATE, $businessProfile);
        return $this->save($task);
    }

    /**
     * Create new 'Update Business Profile' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function createUpdateProfileConfirmationRequest(BusinessProfile $businessProfile) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_UPDATE, $businessProfile);
        return $this->save($task);
    }

    /**
     * Create new 'Close Business Profile' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function createCloseProfileConfirmationRequest(BusinessProfile $businessProfile) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_CLOSE, $businessProfile);
        return $this->save($task);
    }

    /**
     * Create new 'Approve Business Review' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function createBusinessReviewConfirmationRequest(BusinessProfile $businessProfile) : array
    {
        //TODO: implement REVIEW object saving here (when we'll have Review entity class)
        $task = TasksFactory::create(TaskType::TASK_REVIEW_APPROVE, $businessProfile);
        return $this->save($task);
    }

    /**
     * Fetch count of approved tasks from db
     *
     * @access public
     * @return int
     */
    public function getTotalApprovedTasksCount() : int
    {
        return $this->repository->getTotalApprovedTasksCount();
    }

    /**
     * Fetch count of rejected tasks from db
     *
     * @access public
     * @return int
     */
    public function getTotalRejectedTasksCount() : int
    {
        return $this->repository->getTotalRejectedTasksCount();
    }

    /**
     * Fetch count of still open tasks from db
     *
     * @access public
     * @return int
     */
    public function getTotalIncompleteTasksCount() : int
    {
        return $this->repository->getTotalIncompleteTasksCount();
    }

    /**
     * Save task entity (call $em->persist() & $em->flush())
     *
     * @access protected
     * @param Task $task
     * @return array
     */
    protected function save(Task $task) : array
    {
        $success = true;
        $message = self::TASK_SUCCESSFULLY_CREATED_MESSAGE;

        try {
            $this->em->persist($task);
            $this->em->flush();
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->buildResponseArray($success, $message);
    }

    /**
     * Make array with response data
     *
     * @access private
     * @param bool $success
     * @param string $message
     * @return array
     */
    private function buildResponseArray(bool $success, string $message) : array
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        return $response;
    }
}
