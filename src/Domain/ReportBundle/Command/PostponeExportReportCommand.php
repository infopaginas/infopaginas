<?php

namespace Domain\ReportBundle\Command;

use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Class PostponeExportReportCommand
 * @package Domain\ReportBundle\Command
 */
class PostponeExportReportCommand extends ContainerAwareCommand
{
    private const POSTPONE_EXPORT_LOCK = 'POSTPONE_EXPORT.lock';

    protected function configure()
    {
        $this
            ->setName('domain:postpone-export:report')
            ->setDescription('Postpone export reports')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockHandler = new LockHandler(self::POSTPONE_EXPORT_LOCK);

        if (!$lockHandler->lock()) {
            return $output->writeln('Command is locked by another process');
        }

        $container = $this->getContainer();
        $logger = $container->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::POSTPONE_EXPORT, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $output->writeln('Start...');
        $postponeExportManager = $container->get('domain_report.manager.postpone_export_report');
        $postponeExportManager->postponeExportReport();
        $output->writeln('done');

        $logger->addInfo(CronLogger::POSTPONE_EXPORT, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

        $lockHandler->release();
    }
}
