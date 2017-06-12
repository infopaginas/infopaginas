<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
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
class ReportArchiveDataCommand extends ContainerAwareCommand
{
    /**
     * Lifetime of mongoDB logs
     */
    const LOGS_LIFETIME = 24;

    protected function configure()
    {
        $this
            ->setName('domain:report-mongo-db:archive')
            ->setDescription('Archive mongoDB reports')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_START, 'execute:start');

        $rawDataArchivingDate = $this->getRawDataArchivingDate();
        $aggregatedDataArchivingDate = $this->getAggregatedDataArchivingDate();

        $output->writeln('Start...');

        $output->writeln('Process overview report');
        $this->getBusinessOverviewReportManager()->archiveRawBusinessInteractions($rawDataArchivingDate);
        $this->getBusinessOverviewReportManager()->archiveAggregatedBusinessInteractions($aggregatedDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process overview report');

        $output->writeln('Process keyword report');
        $this->getKeywordsReportManager()->archiveRawBusinessKeywords($rawDataArchivingDate);
        $this->getKeywordsReportManager()->archiveAggregatedBusinessKeywords($aggregatedDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

        $output->writeln('Process category report');
        $this->getCategoryReportManager()->archiveRawBusinessCategories($rawDataArchivingDate);
        $this->getCategoryReportManager()->archiveAggregatedBusinessCategories($aggregatedDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process category report');

        $output->writeln('done');
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * @return KeywordsReportManager
     */
    protected function getKeywordsReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.keywords_report_manager');
    }

    /**
     * @return CategoryReportManager
     */
    protected function getCategoryReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.category_report_manager');
    }

    /**
     * @return \Datetime
     */
    protected function getRawDataArchivingDate()
    {
        return DatesUtil::getYesterday();
    }

    /**
     * @return \Datetime
     */
    protected function getAggregatedDataArchivingDate()
    {
        return DatesUtil::getYesterday()->modify('-' . self::LOGS_LIFETIME . ' month');
    }
}
