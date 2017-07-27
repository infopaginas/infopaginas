<?php

namespace Domain\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class ResetElasticCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('data:elastic:reset');
        $this->setDescription('Reset elastic search');
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');
        $businessProfileManager->handleElasticSearchIndexRefresh();
    }
}
