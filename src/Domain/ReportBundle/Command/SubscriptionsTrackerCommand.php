<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SubscriptionsTrackerCommand
 * @package Domain\ReportBundle\Command
 */
class SubscriptionsTrackerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ipgn:subscriptions:track')
            ->setDescription('Track information about active subscription.')
            ->setHelp("Subscriptions data required for subscriptions report...")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::SUBSCRIPTION_TRACK, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $output->writeln('Start stats calculation...');
        $this->getSubscriptionReportManager()->saveSubscriptionStats();
        $output->writeln('..done!');

        $logger->addInfo(CronLogger::SUBSCRIPTION_TRACK, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);
    }

    /**
     * @return SubscriptionReportManager
     */
    protected function getSubscriptionReportManager() : SubscriptionReportManager
    {
        return $this->getContainer()->get('domain_report.manager.subscription_report_manager');
    }
}
