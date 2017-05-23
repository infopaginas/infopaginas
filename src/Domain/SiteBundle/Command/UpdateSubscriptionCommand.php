<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\EventListener\ElasticSearchSubscriber;
use Domain\BusinessBundle\EventListener\SubscriptionListener;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\StatusInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSubscriptionCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var $statusManager SubscriptionStatusManager
     */
    private $statusManager;

    /**
     * @var $statusManager BusinessProfileManager
     */
    private $businessProfileManager;

    protected function configure()
    {
        $this->setName('data:subscription:update');
        $this->setDescription('Update businesses subscription');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::SUBSC_UPDATE, $logger::STATUS_START, 'execute:start');

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->statusManager = $this->getContainer()->get('domain_business.manager.subscription_status_manager');
        $this->businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $this->update();
        $logger->addInfo($logger::SUBSC_UPDATE, $logger::STATUS_END, 'execute:stop');
    }

    public function update()
    {
        $this->disableSubscriptionEventListener();

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
        }

        $this->em->flush();

        //update elastic
        $this->businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');
        $this->businessProfileManager->handleElasticSearchIndexRefresh();
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
                if ($listener instanceof SubscriptionListener or $listener instanceof ElasticSearchSubscriber) {
                    $this->em->getEventManager()->removeEventListener($eventName, $listener);
                }
            }
        }
    }
}
