<?php

namespace Domain\BusinessBundle\Command;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $datetimePeriodService = $this->getContainer()->get('domain_business.service.datetime_period_status_service');
        $output->writeln('Processing...');
        $result = $datetimePeriodService->updateStatus();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));
    }
}