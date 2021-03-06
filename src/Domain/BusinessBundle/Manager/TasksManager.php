<?php

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
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
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
     *
     * @return array
     */
    public function createNewProfileConfirmationRequest(BusinessProfile $businessProfile) : array
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_CREATE, $businessProfile);
        return $this->save($task);
    }

    /**
     * @param mixed $entityNew
     * @param mixed $entityOld
     *
     * @return array
     */
    public function createUpdateProfileConfirmationRequest($entityNew, $entityOld) : array
    {
        $changeSetCalculator = $this->getChangeSetCalculator($entityNew);

        if ($changeSetCalculator) {
            $changeSet = $changeSetCalculator::getChangeSet($this->em, $entityNew, $entityOld);
        } else {
            $changeSet = null;
        }

        if ($changeSet and $changeSet->getEntries()->isEmpty()) {
            return [];
        }

        $task = TasksFactory::create(TaskType::TASK_PROFILE_UPDATE, $entityOld);

        $task->setChangeSet($changeSet);

        $result = $this->save($task, false);

        return $result;
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
     * @param BusinessProfile   $businessProfile
     * @param string            $message
     *
     * @return array
     */
    public function createClaimProfileConfirmationRequest($businessProfile, $message)
    {
        $task = TasksFactory::create(TaskType::TASK_PROFILE_CLAIM, $businessProfile);
        $task->setClosureReason($message);

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
        $mailer = $this->getMailer();

        if ($task->getType() == TaskType::TASK_PROFILE_CREATE) {
            $this->getBusinessProfileManager()->activate($businessProfile);

            if ($businessProfile->getUser()) {
                $this->getUsersManager()->changeUserRole(
                    $businessProfile->getUser(),
                    Group::CODE_MERCHANT,
                    Group::CODE_CONSUMER
                );
            }
            $mailer->sendStatusWasChangedEmailMessage($businessProfile, BusinessProfile::USER_STATUS_ACCEPTED);
        } elseif ($task->getType() == TaskType::TASK_PROFILE_UPDATE) {
            $this->getBusinessProfileManager()->publish($task->getBusinessProfile(), $task->getChangeSet());
            $mailer->sendStatusWasChangedEmailMessage($businessProfile, BusinessProfile::USER_STATUS_ACCEPTED);
        } elseif ($task->getType() == TaskType::TASK_REVIEW_APPROVE) {
            $this->getBusinessReviewsManager()->publish($task->getReview());
        } elseif ($task->getType() == TaskType::TASK_PROFILE_CLOSE) {
            $this->getBusinessProfileManager()->deactivate($task->getBusinessProfile());
            $mailer->sendStatusWasChangedEmailMessage($businessProfile, BusinessProfile::USER_STATUS_DEACTIVATED);
        } elseif ($task->getType() == TaskType::TASK_PROFILE_CLAIM) {
            $this->getBusinessProfileManager()->claim($task->getBusinessProfile(), $task->getCreatedUser());
            $this->rejectOtherClaimRequests($task->getBusinessProfile()->getId(), $task->getId());
        }

        return $this->save($task);
    }

    /**
     * @param int $businessProfileId
     * @param int $currentTaskId
     */
    public function rejectOtherClaimRequests($businessProfileId, $currentTaskId)
    {
        $tasks = $this->em->getRepository(Task::class)
            ->getOtherClaimRequestsForBusiness($businessProfileId, $currentTaskId);

        foreach ($tasks as $task) {
            $task->setRejectReason(Task::REJECT_REASON_BUSINESS_ALREADY_CLAIMED);
            $this->reject($task);
        }
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
            case TaskType::TASK_PROFILE_UPDATE:
            case TaskType::TASK_PROFILE_CLOSE:
                $this->getMailer()->sendStatusWasChangedEmailMessage(
                    $businessProfile,
                    BusinessProfile::USER_STATUS_REJECTED,
                    $rejectReason
                );
                break;
            case TaskType::TASK_REVIEW_APPROVE:
                $review = $task->getReview();
                $this->getMailer()->sendBusinessProfileReviewRejectEmailMessage($review, $rejectReason);
                break;
            case TaskType::TASK_PROFILE_CLAIM:
                $this->getMailer()->sendBusinessProfileClaimRejectEmailMessage($task, $rejectReason);
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

    private function getChangeSetCalculator($entity)
    {
        if ($entity instanceof BusinessProfile) {
            $changeSetCalculator = new ChangeSetCalculator();
        } else {
            $changeSetCalculator = null;
        }

        return $changeSetCalculator;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string          $panoramaId
     *
     * @return bool
     */
    public function createAddPanoramaTask(BusinessProfile $businessProfile, $panoramaId)
    {
        $changeSetCalculator = $this->getChangeSetCalculator($businessProfile);

        $changeSet = $changeSetCalculator::getPanoramaChangeSet($this->em, $businessProfile, $panoramaId);

        if (!$changeSet or ($changeSet and $changeSet->getEntries()->isEmpty())) {
            return [];
        }

        $task = TasksFactory::create(TaskType::TASK_PROFILE_UPDATE, $businessProfile);

        $task->setChangeSet($changeSet);

        $result = $this->save($task, false);

        return $result;
    }
}
