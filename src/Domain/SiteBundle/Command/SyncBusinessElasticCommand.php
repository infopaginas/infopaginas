<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncBusinessElasticCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var EntityManager $em
     */
    protected $em;

    protected $withDebug;

    protected function configure()
    {
        $this->setName('data:elastic:sync');
        $this->setDescription('Synchronize business with elastic search');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $businessProfileManager->handleBusinessElasticSync();
    }
}
