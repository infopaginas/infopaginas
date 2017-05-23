<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 16.09.16
 * Time: 17:10
 */

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
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
        $logger->addInfo($logger::SUBSCRIPTION_TRACK, $logger::STATUS_START, 'execute:start');

        $output->writeln('Start stats calculation...');
        $this->getSubscriptionReportManager()->saveSubscriptionStats();
        $output->writeln('..done!');

        $logger->addInfo($logger::SUBSCRIPTION_TRACK, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return SubscriptionReportManager
     */
    protected function getSubscriptionReportManager() : SubscriptionReportManager
    {
        return $this->getContainer()->get('domain_report.manager.subscription_report_manager');
    }
}
