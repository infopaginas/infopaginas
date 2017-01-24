<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\EventListener\SubscriptionListener;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\StatusInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170124132041 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;


    /**
     * @var $statusManager SubscriptionStatusManager
     */
    private $statusManager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->statusManager = $this->container->get('domain_business.manager.subscription_status_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->disableSubscriptionEventListener();

        $this->em->flush();
        $this->em->clear();

        $businesses = $this->getBusinessIteratorWithExcessSubscription();

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var $business BusinessProfile */
            $business = $row[0];

            $activeSubscriptions = $this->em->getRepository('DomainBusinessBundle:Subscription')
                ->getActualSubscriptionsForBusiness($business);


            if ($activeSubscriptions) {
                $prioritySubscription = $this->statusManager->getPrioritySubscription($activeSubscriptions);

                if ($prioritySubscription) {
                    foreach ($activeSubscriptions as $subscription) {
                        if ($subscription->getId() != $prioritySubscription->getId()) {
                            $subscription->setStatus(StatusInterface::STATUS_PENDING);
                        }
                    }
                }
            } else {
                $this->statusManager->setBusinessProfileFreeSubscription($business, $this->em);
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
            $i ++;

            $this->em->detach($row[0]);
        }

        $this->em->flush();

        //update elastic
        $businessProfileManager = $this->container->get('domain_business.manager.business_profile');
        $businessProfileManager->handleElasticSearchIndexRefresh();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function getBusinessIteratorWithExcessSubscription()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->join('b.subscriptions', 's')
            ->where('s.status = :status')
            ->andHaving('COUNT(s.id) > 1')
            ->groupBy('b.id')
            ->setParameter(':status', StatusInterface::STATUS_ACTIVE)
        ;

        $businesses = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($businesses as $business) {
            $data[] = $business['id'];
        }

        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->where('b.id IN (:ids)')
            ->setParameter('ids', $data)
        ;

        $query = $this->em->createQuery($qb->getDQL());
        $query->setParameter('ids', $data);

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    protected function disableSubscriptionEventListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof SubscriptionListener) {
                    $this->em->getEventManager()->removeEventListener($eventName, $listener);
                }
            }
        }
    }
}
