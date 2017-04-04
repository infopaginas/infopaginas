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

    /**
     * Used manage objects statuses
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $output->writeln('Processing...');
        $result = $businessProfileManager->updatedMangedBusinessCounter();
        $output->writeln(sprintf('Done! Updated records count: %s', $result));
    }
}
