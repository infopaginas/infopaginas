<?php

namespace Domain\BusinessBundle\Command;

use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class PeriodStatusCommand
 * @package Domain\BusinessBundle\Command
 */
class PeriodStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('domain:business:update-status')
            ->setDescription('Update status for expired datetime records')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::BUSINESS_UPDATE_STATUS, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $datetimePeriodService = $this->getContainer()->get('domain_business.service.datetime_period_status_service');

        $output->writeln('Processing...');
        $result = $datetimePeriodService->updateStatus();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));

        $output->writeln('Create active subscriptions...');
        $result = $datetimePeriodService->createActiveSubscriptions();
        $output->writeln(sprintf('Done! Created records count: %s', $result));

        $output->writeln('Check duplicate of active subscriptions...');
        $result = $datetimePeriodService->updateActiveSubscriptions();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));

        $logger->addInfo(CronLogger::BUSINESS_UPDATE_STATUS, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);
    }
}
