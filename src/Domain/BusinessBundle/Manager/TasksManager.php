<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 16.05.16
 * Time: 15:41
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Model\Task\TasksFactory;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\Sonata\UserBundle\Entity\Group;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\Sonata\UserBundle\Manager\UsersManager;

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

    /** @var BusinessReviewManager $businessReviewManager */
    protected $businessReviewManager;

    /** @var UsersManager $usersManager */
    protected $usersManager;

    /** @var Mailer $mailer */
    protected $mailer;

    /**
     * TasksManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     * @param BusinessProfileManager $businessProfileManager
     * @param UsersManager $usersManager
     */
    public function __construct(
        EntityManager $entityManager,
        BusinessProfileManager $businessProfileManager,
        BusinessReviewManager $businessReviewManager,
        UsersManager $usersManager,
        Mailer $mailer
    ) {
        $this->em         = $entityManager;
        $this->repository = $this->em->getRepository(Task::class);

        $this->businessProfileManager = $businessProfileManager;
        $this->businessReviewManager  = $businessReviewManager;
        $this->usersManager           = $usersManager;
        $this->mailer                 = $mailer;
    }

    /**
     * Create new 'Create Business Profile' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @param Collection      $oldCategories
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
     * @param Collection      $oldCategories
     * @return array
     */
    public function createUpdateProfileConfirmationRequest(BusinessProfile $businessProfile, $oldCategories) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_UPDATE, $businessProfile);
        $task->setChangeSet(ChangeSetCalculator::getChangeSet($this->em, $businessProfile, $oldCategories));
        return $this->save($task, false);
    }

    /**
     * Create new 'Close Business Profile' task
     *
     * @access public
     * @param BusinessProfile $businessProfile
     * @param string $closeReason
     * @return array
     */
    public function createCloseProfileConfirmationRequest(BusinessProfile $businessProfile, string $closeReason) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_CLOSE, $businessProfile);
        $task->setClosureReason($closeReason);
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

        $this->notifyUserAboutReject($task);

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

        $businessProfile = $task->getBusinessProfile();

        if ($task->getType() == TaskType::TASK_PROFILE_CREATE) {
            $this->getBusinessProfileManager()->activate($businessProfile);

            if ($businessProfile->getUser()) {
                $this->getUsersManager()->changeUserRole(
                    $businessProfile->getUser(),
                    Group::CODE_MERCHANT,
                    Group::CODE_CONSUMER
                );
            }
        } elseif ($task->getType() == TaskType::TASK_PROFILE_UPDATE) {
            $this->getBusinessProfileManager()->publish($task->getBusinessProfile(), $task->getChangeSet(), $this->getTaskLocale($task));
        } elseif ($task->getType() == TaskType::TASK_REVIEW_APPROVE) {
            $this->getBusinessReviewsManager()->publish($task->getReview());
        } elseif ($task->getType() == TaskType::TASK_PROFILE_CLOSE) {
            $this->getBusinessProfileManager()->deactivate($task->getBusinessProfile());
        }

        return $this->save($task);
    }

    /**
     * @param Task $task
     * @return mixed|string
     */
    private function getTaskLocale(Task $task)
    {
        return empty($task->getLocale()) ? BusinessProfile::DEFAULT_LOCALE : $task->getLocale();
    }

    /**
     * Save task entity (call $em->persist() & $em->flush())
     *
     * @param Task $task
     * @param bool $updateRelated
     * @return array
     */
    protected function save(Task $task, $updateRelated = true) : array
    {
        $success = true;
        $message = self::TASK_SUCCESSFULLY_CREATED_MESSAGE;

        try {
            $this->em->persist($task);
            if ($updateRelated) {
                $this->em->flush();
            } else {
                $this->em->flush($task);
            }
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->buildResponseArray($success, $message);
    }

    protected function notifyUserAboutReject(Task $task)
    {
        $businessProfile = $task->getBusinessProfile();
        $rejectReason    = $task->getRejectReason();

        switch ($task->getType()) {
            case TaskType::TASK_PROFILE_CREATE:
                $this->getMailer()->sendBusinessProfileCreateRejectEmailMessage($businessProfile, $rejectReason);
                break;
            case TaskType::TASK_PROFILE_UPDATE:
                $this->getMailer()->sendBusinessProfileUpdateRejectEmailMessage($businessProfile, $rejectReason);
                break;
            case TaskType::TASK_PROFILE_CLOSE:
                $this->getMailer()->sendBusinessProfileCloseRejectEmailMessage($businessProfile, $rejectReason);
                break;
            case TaskType::TASK_REVIEW_APPROVE:
                $review = $task->getReview();
                $this->getMailer()->sendBusinessProfileReviewRejectEmailMessage($review, $rejectReason);
                break;
        }
    }

    /**
     * @return Mailer
     */
    private function getMailer() : Mailer
    {
        return $this->mailer;
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
     * @return BusinessReviewManager
     */
    private function getBusinessReviewsManager() : BusinessReviewManager
    {
        return $this->businessReviewManager;
    }

    /**
     * @return UsersManager
     */
    private function getUsersManager() : UsersManager
    {
        return $this->usersManager;
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
