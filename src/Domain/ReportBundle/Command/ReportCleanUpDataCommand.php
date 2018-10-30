<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReportCleanUpDataCommand
 * @package Domain\ReportBundle\Command
 */
class ReportCleanUpDataCommand extends ContainerAwareCommand
{
    /**
     * Lifetime of mongoDB logs
     */
    const LOGS_LIFETIME = 5;

    protected function configure()
    {
        $this
            ->setName('domain:report-mongo-db:clean-up')
            ->setDescription('Clean up mongoDB reports')
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
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_START, 'execute:start');

        $rawDataArchivingDate = $this->getRawDataDeletionDate();

        $output->writeln('Start...');

        $output->writeln('Process overview report');
        $this->getBusinessReportManager()->deleteArchivedRawBusinessInteractions($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process overview report');

        $output->writeln('Process keyword report');
        $this->getKeywordReportManager()->deleteArchivedRawKeywordData($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

        $output->writeln('Process category report');
        $this->getCategoryReportManager()->deleteArchivedRawCategoryData($rawDataArchivingDate);
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_IN_PROGRESS, 'execute:Process category report');

        $output->writeln('done');
        $logger->addInfo($logger::MONGO_ARCHIVE, $logger::STATUS_END, 'execute:stop');
    }

    /**
     * @return \Datetime
     */
    protected function getRawDataDeletionDate()
    {
        return DatesUtil::getYesterday()->modify('-' . self::LOGS_LIFETIME . ' days');
    }

    /**
     * @return \Domain\ReportBundle\Manager\BusinessOverviewReportManager
     */
    protected function getBusinessReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * @return \Domain\ReportBundle\Manager\KeywordsReportManager
     */
    protected function getKeywordReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.keywords_report_manager');
    }

    /**
     * @return \Domain\ReportBundle\Manager\CategoryOverviewReportManager
     */
    protected function getCategoryReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.category_overview_report_manager');
    }
}
