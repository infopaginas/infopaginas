<?php

namespace Domain\BusinessBundle\Command;

use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ManagedBusinessesCounterCommand
 * @package Domain\BusinessBundle\Command
 */
class ManagedBusinessesCounterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('domain:managed-businesses:counter')
            ->setDescription('Update amount of managed businesses')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::BUSINESS_COUNTER, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $output->writeln('Processing...');
        $result = $businessProfileManager->updatedManagedBusinessesCounter();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));

        $logger->addInfo(CronLogger::BUSINESS_COUNTER, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);
    }
}
