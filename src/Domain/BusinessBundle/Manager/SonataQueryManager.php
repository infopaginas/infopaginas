<?php

namespace Domain\BusinessBundle\Manager;

use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * Class SonataQueryManager
 * @package Domain\BusinessBundle\Manager
 */
class SonataQueryManager
{
    /**
     * @var ModelManagerInterface
     */
    protected $modelManager;

    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    /**
     * @param int|null $businessProfileId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getBusinessSubscriptionQuery(int $businessProfileId = null)
    {
        $query = $this->modelManager->getEntityManager('DomainBusinessBundle:Subscription')
            ->createQueryBuilder()
            ->select('s')
            ->from('DomainBusinessBundle:Subscription', 's')
            ->leftJoin('s.businessProfile', 'bp')
            ->andWhere('bp.id = :businessProfileId')
            ->setParameter('businessProfileId', $businessProfileId)
            ->orderBy('s.id', 'DESC')
        ;

        return $query;
    }
}
