<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;

/**
 * TaskRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaskRepository extends \Doctrine\ORM\EntityRepository
{
    const SLUG = 'DomainBusinessBundle:Task';

    /**
     * Get count of closed tasks
     *
     * @access public
     * @return int
     */
    public function getTotalApprovedTasksCount() : int
    {
        $count = $this->getTotalTasksCountByProvidedType(TaskStatusType::TASK_STATUS_CLOSED);
        return $count;
    }

    /**
     * Get count of rejected tasks
     *
     * @access public
     * @return int
     */
    public function getTotalRejectedTasksCount() : int
    {
        $count = $this->getTotalTasksCountByProvidedType(TaskStatusType::TASK_STATUS_REJECTED);
        return $count;
    }

    /**
     * Get count of open tasks
     *
     * @access public
     * @return int
     */
    public function getTotalIncompleteTasksCount() : int
    {
        $count = $this->getTotalTasksCountByProvidedType(TaskStatusType::TASK_STATUS_OPEN);
        return $count;
    }

    /**
     * Get COUNT() of tasks from db based on provided Task type
     *
     * @access private
     * @param string $status
     * @return int|mixed
     */
    private function getTotalTasksCountByProvidedType(string $status)
    {
        $qb = $this->getQueryBuilder();

        try {
            $count = $qb->select('COUNT(task.id) as tasksCount')
                ->where('task.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            $count = 0;
        }

        return $count;
    }

    /**
     * Return query builder object (incapsulate in single method)
     *
     * @access private
     * @return QueryBuilder
     */
    private function getQueryBuilder() : QueryBuilder
    {
        return $this->createQueryBuilder('task');
    }
}
