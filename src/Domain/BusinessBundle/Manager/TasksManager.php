<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 16.05.16
 * Time: 15:41
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Model\Task\TasksFactory;
use Domain\BusinessBundle\Repository\TaskRepository;
use Oxa\Sonata\UserBundle\Entity\User;

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

    /** @var BusinessProfileManager */
    protected $businessProfileManager;

    /**
     * TasksManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(EntityManager $entityManager, BusinessProfileManager $businessProfileManager)
    {
        $this->em = $entityManager;

        $this->repository = $this->em->getRepository(TaskRepository::SLUG);

        $this->businessProfileManager = $businessProfileManager;
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
     * @param BusinessReview $businessReview
     * @return array
     */
    public function createBusinessReviewConfirmationRequest(BusinessReview $businessReview) : array
    {
        $businessProfile = $businessReview->getBusinessProfile();
        $task = TasksFactory::create(TaskType::TASK_REVIEW_APPROVE, $businessProfile, $businessReview);
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
     * Associate user object with task
     *
     * @access public
     * @param Task $task
     * @param User $reviewer
     * @return array
     */
    public function setReviewerForTask(Task $task, User $reviewer) : array
    {
        $task->setReviewer($reviewer);
        return $this->save($task);
    }

    /**
     * Set "Rejected" status for task
     *
     * @access public
     * @param Task $task
     * @return array
     */
    public function reject(Task $task) : array
    {
        $task->setStatus(TaskStatusType::TASK_STATUS_REJECTED);
        return $this->save($task);
    }

    /**
     * Set "Closed" (== APPROVED) status for task
     *
     * @access public
     * @param Task $task
     * @return array
     */
    public function approve(Task $task) : array
    {
        $task->setStatus(TaskStatusType::TASK_STATUS_CLOSED);

        if ($task->getType() == TaskType::TASK_PROFILE_CREATE) {
            $this->getBusinessProfileManager()->activate($task->getBusinessProfile());
        } else {
            $this->getBusinessProfileManager()->publish($task->getBusinessProfile());
        }

        return $this->save($task);
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
     * Provide access to business profiles manager object
     *
     * @access private
     * @return BusinessProfileManager
     */
    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
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
