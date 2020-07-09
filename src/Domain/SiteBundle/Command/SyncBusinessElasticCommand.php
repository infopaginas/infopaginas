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
    private const ELASTIC_SYNC_LOCK = 'ELASTIC_SYNC.lock';

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var EntityManager $em
     */
    protected $em;

    protected function configure()
    {
        $this->setName('data:elastic:sync');
        $this->setDescription('Synchronize business with elastic search');
        $this->addArgument('object', InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::ELASTIC_SYNC, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $lockHandler = new LockHandler(self::ELASTIC_SYNC_LOCK);

        if (!$lockHandler->lock()) {
            $logger->addInfo(CronLogger::ELASTIC_SYNC, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

            return $output->writeln('Command is locked by another process');
        }

        $businessProfileManager = $this->getContainer()->get('domain_business.manager.business_profile');

        $object = $input->getArgument('object');

        if (!$object) {
            $businessProfileManager->handleLocalityElasticSync();
            $businessProfileManager->handleCategoryElasticSync();
            $businessProfileManager->handleBusinessElasticSync();
            $businessProfileManager->handleEmergencyBusinessElasticSync();
        } else {
            switch ($object) {
                case 'business':
                    $businessProfileManager->handleBusinessElasticSync();
                    break;
                case 'category':
                    $businessProfileManager->handleCategoryElasticSync();
                    break;
                case 'locality':
                    $businessProfileManager->handleLocalityElasticSync();
                    break;
                case 'emergency':
                    $businessProfileManager->handleEmergencyBusinessElasticSync();
                    break;
            }
        }

        $lockHandler->release();
        $logger->addInfo(CronLogger::ELASTIC_SYNC, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);
    }
}
