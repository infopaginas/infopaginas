<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
 * Class ReportArchiveDataCommand
 * @package Domain\ReportBundle\Command
 */
class ReportArchiveRawDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName(CronLogger::MONGO_ARCHIVE_RAW)
            ->setDescription('Archive mongoDB reports (raw)')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $store = new SemaphoreStore();
        $factory = new Factory($store);
        $lock = $factory->createLock(CronLogger::MONGO_ARCHIVE_RAW);

        if ($lock->acquire()) {
            $container = $this->getContainer();
            $logger = $container->get('domain_site.cron.logger');
            $logger->addInfo(CronLogger::MONGO_ARCHIVE_RAW, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

            $rawDataArchivingDate = $this->getRawDataArchivingDate();

            $output->writeln('Start...');

            $output->writeln('Process overview report');
            $businessOverviewReportManager = $container->get('domain_report.manager.business_overview_report_manager');
            $businessOverviewReportManager->archiveRawBusinessInteractions($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_ARCHIVE_RAW, CronLogger::STATUS_IN_PROGRESS, 'execute:Process overview report');

            $output->writeln('Process keyword report');
            $keywordsReportManager = $container->get('domain_report.manager.keywords_report_manager');
            $keywordsReportManager->archiveRawBusinessKeywords($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_ARCHIVE_RAW, CronLogger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

            $output->writeln('Process category report');
            $categoryOverviewReportManager = $container->get('domain_report.manager.category_overview_report_manager');
            $categoryOverviewReportManager->archiveRawBusinessCategories($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_ARCHIVE_RAW, CronLogger::STATUS_IN_PROGRESS, 'execute:Process category report');

            $output->writeln('done');
            $logger->addInfo(CronLogger::MONGO_ARCHIVE_RAW, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

            $lock->release();
        } else {
            return $output->writeln('Command is locked by another process');
        }

        return 0;
    }

    /**
     * @return \Datetime
     */
    protected function getRawDataArchivingDate()
    {
        return DatesUtil::getYesterday();
    }
}
