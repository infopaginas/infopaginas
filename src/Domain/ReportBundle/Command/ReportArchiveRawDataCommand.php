<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReportArchiveDataCommand
 * @package Domain\ReportBundle\Command
 */
class ReportArchiveRawDataCommand extends ContainerAwareCommand
{
    /**
     * Lifetime of mongoDB logs
     */
    const LOGS_LIFETIME = 24;

    protected function configure()
    {
        $this
            ->setName('domain:report-mongo-db:archive-raw')
            ->setDescription('Archive mongoDB reports (raw)')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('domain_site.cron.logger');
        $logger->addInfo($logger::MONGO_ARCHIVE_RAW, $logger::STATUS_START, 'execute:start');

        $rawDataArchivingDate = $this->getRawDataArchivingDate();

        $output->writeln('Start...');

        $output->writeln('Process overview report');
        $businessOverviewReportManager = $container->get('domain_report.manager.business_overview_report_manager');
        $businessOverviewReportManager->archiveRawBusinessInteractions($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process overview report');

        $output->writeln('Process keyword report');
        $keywordsReportManager = $container->get('domain_report.manager.keywords_report_manager');
        $keywordsReportManager->archiveRawBusinessKeywords($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

        $output->writeln('Process category report');
        $categoryOverviewReportManager = $container->get('domain_report.manager.category_overview_report_manager');
        $categoryOverviewReportManager->archiveRawBusinessCategories($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process category report');

        $output->writeln('done');
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return \Datetime
     */
    protected function getRawDataArchivingDate()
    {
        return DatesUtil::getYesterday();
    }
}
