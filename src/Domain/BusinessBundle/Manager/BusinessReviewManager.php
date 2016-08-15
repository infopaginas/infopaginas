<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;

/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 12.08.16
 * Time: 11:26
 */
class BusinessReviewManager extends \Oxa\ManagerArchitectureBundle\Model\Manager\Manager
{
    /**
     * BusinessReviewManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @param BusinessReview $review
     * @return string
     */
    public function computeReviewerUsername(BusinessReview $review) : string
    {
        if ($review->getUsername()) {
            return $review->getUsername();
        }

        $username = $review->getUser()->getFirstname() . ' ' . $review->getUser()->getLastname();
        return $username;
    }

    /**
     * @param BusinessReview $review
     */
    public function publish(BusinessReview $review)
    {
        $review->setIsActive(true);
        $this->commit($review);
    }

    /**
     * @param BusinessReview $review
     */
    public function save(BusinessReview $review)
    {
        $this->commit($review);
    }

    public function getRepository()
    {
        return $this->em->getRepository(BusinessReviewRepository::SLUG);
    }

    /**
     * @param BusinessReview $review
     */
    private function commit(BusinessReview $review)
    {
        $this->getEntityManager()->persist($review);
        $this->getEntityManager()->flush();
    }
}
