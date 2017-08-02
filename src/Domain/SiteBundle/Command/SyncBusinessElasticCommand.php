<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class SyncBusinessElasticCommand extends ContainerAwareCommand
{
    const ELASTIC_SYNC_LOCK = 'ELASTIC_SYNC.lock';

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

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::ELASTIC_SYNC, $logger::STATUS_START, 'execute:start');

        $lockHandler = new LockHandler(self::ELASTIC_SYNC_LOCK);

        if (!$lockHandler->lock()) {
            $logger->addInfo($logger::ELASTIC_SYNC, $logger::STATUS_END, 'execute:stop');

            return $output->writeln('Command is locked by another process');
        }

        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $businessProfileManager->handleLocalityElasticSync();
        $businessProfileManager->handleCategoryElasticSync();
        $businessProfileManager->handleBusinessElasticSync();

        $lockHandler->release();
        $logger->addInfo($logger::ELASTIC_SYNC, $logger::STATUS_END, 'execute:stop');
    }
}
