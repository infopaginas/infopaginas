<?php

namespace Domain\BusinessBundle\Command;

use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Class ProcessCSVImportFileCommand
 * @package Domain\BusinessBundle\Command
 */
class ProcessCSVImportFileCommand extends ContainerAwareCommand
{
    private const PROCESS_CSV_FILE_LOCK = 'PROCESS_CSV_FILE_LOCK.lock';

    protected function configure()
    {
        $this
            ->setName('domain:process-csv-file')
            ->setDescription('Process CSV Import files')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockHandler = new LockHandler(self::PROCESS_CSV_FILE_LOCK);

        if (!$lockHandler->lock()) {
            return $output->writeln('Command is locked by another process');
        }

        $container = $this->getContainer();
        $logger = $container->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::POSTPONE_EXPORT, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $output->writeln('Start...');

        $csvImportFileManager = $container->get('domain_business.manager.csv_import_file_manager');
        $csvImportFileManager->processCSVImportFiles();

        $output->writeln('done');

        $logger->addInfo(CronLogger::POSTPONE_EXPORT, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

        $lockHandler->release();
    }
}
