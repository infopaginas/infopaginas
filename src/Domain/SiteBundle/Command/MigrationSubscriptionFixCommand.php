<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\EventListener\DatetimePeriodStatusSubscriber;
use Domain\BusinessBundle\EventListener\ElasticSearchSubscriber;
use Domain\BusinessBundle\EventListener\SubscriptionListener;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationSubscriptionFixCommand extends ContainerAwareCommand
{
    const DEFAULT_LOCALE  = 'en';

    const API_BASE_URL    = 'http://infopaginas.drxlive.com/api/businesses';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var bool $withDebug
     */
    protected $withDebug;

    /**
     * @var array $subscriptionPlans
     */
    protected $subscriptionPlans = [];

    /**
     * @var array $missingSubscriptions
     */
    protected $missingSubscriptions = [];

    /**
     * @var SubscriptionStatusManager $subscriptionManager
     */
    protected $subscriptionManager = [];

    protected function configure()
    {
        $this->setName('data:migration:subscription-fix');
        $this->setDescription('Update subscriptions');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->subscriptionManager = $this->getContainer()->get('domain_business.manager.subscription_status_manager');

        $this->disableSubscriptionEventListener();

        if ($input->getOption('pageStart')) {
            $pageStart = $input->getOption('pageStart');
        } else {
            $pageStart = 1;
        }

        if ($input->getOption('pageCountLimit')) {
            $pageCountLimit = $input->getOption('pageCountLimit');
        } else {
            $pageCountLimit = 1;
        }

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        $this->subscriptionPlans = $this->getSubscriptionPlans();

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                $output->writeln('Start request page number ' . $page);

                $this->printMissingSubscription();
            }

            $data = $this->getCurlData($this->getBusinessesByPageUrl($page), self::DEFAULT_LOCALE);

            if ($data) {
                foreach ($data as $item) {
                    $itemId = $item->_id;

                    /* @var BusinessProfile $businessProfile */
                    $businessProfile = $this->em->getRepository(BusinessProfile::class)->findOneBy(
                        [
                            'uid' => $itemId,
                        ]
                    );

                    if ($businessProfile) {
                        $subscriptions = $this->getCurlData(
                            $this->getBusinessSubscriptionsByUid($itemId),
                            self::DEFAULT_LOCALE
                        );

                        $businessProfile = $this->removeOldSubscriptions($businessProfile);

                        $this->updateBusinessSubscriptions($subscriptions, $businessProfile);

                        $this->handleDefaultSubscription($businessProfile);

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip item with id ' . $itemId);
                        }
                    }
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        if ($this->withDebug) {
            $output->writeln('Finish requests');
            $this->printMissingSubscription();
        }
    }

    /**
     * @param string $url
     * @param string $locale
     *
     * @return mixed
     */
    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale,
        ]);

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    /**
     * @param mixed             $subscriptions
     * @param BusinessProfile   $businessProfile
     */
    private function updateBusinessSubscriptions($subscriptions, $businessProfile)
    {
        if ($subscriptions) {
            foreach ($subscriptions->subscriptions as $item) {
                $key = $item->plan->contract_id;

                if (isset($this->subscriptionPlans[$key])) {
                    $subscription = new Subscription();

                    $subscriptionPlan = $this->em->getReference(SubscriptionPlan::class, $this->subscriptionPlans[$key]);

                    $subscription->setSubscriptionPlan($subscriptionPlan);
                    $subscription->setBusinessProfile($businessProfile);
                    $subscription->setStartDate(new \DateTime($item->current_period_started_at));

                    $endDate = new \DateTime($item->current_period_ends_at);
                    $now     = new \DateTime();

                    $subscription->setEndDate($endDate);

                    if ($endDate >= $now) {
                        $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
                    } else {
                        $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_EXPIRED);
                    }

                    $businessProfile->addSubscription($subscription);
                } else {
                    if ($this->withDebug) {
                        $this->output->writeln('Unknown subscription Plan:' . json_encode($item));

                        if (empty($this->missingSubscriptions[$key])) {
                            $this->missingSubscriptions[$key] = 1;
                        } else {
                            $this->missingSubscriptions[$key] ++;
                        }

                    }
                }
            }
        }
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    private function getBusinessesByPageUrl($pageNumber)
    {
        return self::API_BASE_URL . '?page=' . $pageNumber;
    }

    /**
     * @param string $uid
     *
     * @return string
     */
    private function getBusinessSubscriptionsByUid($uid)
    {
        return self::API_BASE_URL . '/' . $uid . '/subscriptions';
    }

    /**
     * @return array
     */
    private function getSubscriptionPlans()
    {
        $subscriptionPlans = [];

        $planMapping = [
            SubscriptionPlanInterface::CODE_FREE                => 'Free',
            SubscriptionPlanInterface::CODE_PRIORITY            => 'Priority',
            SubscriptionPlanInterface::CODE_PREMIUM_PLUS        => 'Premium Plus',
            SubscriptionPlanInterface::CODE_PREMIUM_GOLD        => 'Premium Gold',
            SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM    => 'Premium Platinum',
            SubscriptionPlanInterface::CODE_SUPER_VM            => 'SuperVM',
        ];

        $plans = $this->em->getRepository(SubscriptionPlan::class)->findAll();

        $this->subscriptionPlans = [];

        foreach ($plans as $item) {
            $subscriptionPlans[$planMapping[$item->getCode()]] = $item->getId();
        }

        return $subscriptionPlans;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessProfile
     */
    private function removeOldSubscriptions($businessProfile)
    {
        $subscriptions = $businessProfile->getSubscriptions();

        foreach ($subscriptions as $subscription) {
            $businessProfile->removeSubscription($subscription);
            $this->em->remove($subscription);
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessProfile
     */
    private function handleDefaultSubscription($businessProfile)
    {
        $subscription = $businessProfile->getSubscription();

        if (!$subscription) {
            $this->subscriptionManager->setBusinessProfileFreeSubscription($businessProfile, $this->em);
        }

        return $businessProfile;
    }

    private function printMissingSubscription()
    {
        if ($this->missingSubscriptions) {
            foreach ($this->missingSubscriptions as $key => $amount) {
                $this->output->writeln('Missing subscription id ' . $key . ', amount ' . $amount);
            }
        }
    }

    protected function disableSubscriptionEventListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof SubscriptionListener or $listener instanceof ElasticSearchSubscriber
                    or $listener instanceof DatetimePeriodStatusSubscriber
                ) {
                    $this->em->getEventManager()->removeEventListener($eventName, $listener);
                }
            }
        }
    }
}
