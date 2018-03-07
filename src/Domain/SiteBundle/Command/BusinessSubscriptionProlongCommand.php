<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class BusinessSubscriptionProlongCommand extends ContainerAwareCommand
{
    const DATE_FORMAT = 'Y-m-d';

    /* @var EntityManager $em */
    protected $em;

    /* @var \DateTime|null */
    protected $dateFrom;

    /* @var \DateTime|null */
    protected $dateTo;

    /* @var \DateTime|null */
    protected $prolongTo;

    protected function configure()
    {
        $this->setName('data:business-subscription:prolong');
        $this->setDescription('Prolong business subscription');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('dateFrom', null, InputOption::VALUE_REQUIRED, 'Subscription expired from'),
                new InputOption('dateTo', null, InputOption::VALUE_REQUIRED, 'Subscription expired to'),
                new InputOption('prolongTo', null, InputOption::VALUE_REQUIRED, 'Subscription prolong to'),
            ])
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->initParams($input);
        $this->updateSubscriptions();
    }

    /**
     * @throws \Exception
     */
    protected function updateSubscriptions()
    {
        $subscriptions = $this->em->getRepository(Subscription::class)->getSubscriptionProlongIterator(
            $this->dateFrom,
            $this->dateTo
        );

        $batchSize = 20;
        $i = 0;

        foreach ($subscriptions as $row) {
            /* @var Subscription $subscription */
            $subscription = $row[0];

            $subscription->setEndDate($this->prolongTo);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
    }

    /**
     * @param InputInterface $input
     * @throws \Exception
     */
    protected function initParams(InputInterface $input)
    {
        $this->dateFrom  = $this->getDateFromParam($input->getOption('dateFrom'), 'dateFrom');
        $this->dateTo    = $this->getDateFromParam($input->getOption('dateTo'), 'dateTo');
        $this->prolongTo = $this->getDateFromParam($input->getOption('prolongTo'), 'prolongTo');
    }

    /**
     * @param $param
     *
     * @return \DateTime
     * @throws \Exception
     */
    protected function getDateFromParam($param, $hint)
    {
        if ($param) {
            $date = \DateTime::createFromFormat(self::DATE_FORMAT, $param);
        } else {
            $date = null;
        }

        if (!$date) {
            throw new \InvalidArgumentException('Params ' . $hint . ' is not valid');
        }

        return $date;
    }
}
