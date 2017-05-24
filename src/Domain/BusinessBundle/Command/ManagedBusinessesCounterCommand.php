<?php

namespace Domain\BusinessBundle\Command;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::BUSINESS_COUNTER, $logger::STATUS_START, 'execute:start');

        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $output->writeln('Processing...');
        $result = $businessProfileManager->updatedManagedBusinessesCounter();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));

        $logger->addInfo($logger::BUSINESS_COUNTER, $logger::STATUS_END, 'execute:stop');
    }
}
