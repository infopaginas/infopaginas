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
 * Class ReportCleanUpDataCommand
 * @package Domain\ReportBundle\Command
 */
class ReportCleanUpDataCommand extends ContainerAwareCommand
{
    /**
     * Lifetime of mongoDB logs
     */
    private const LOGS_LIFETIME = 5;

    protected function configure()
    {
        $this
            ->setName(CronLogger::MONGO_CLEAN_UP)
            ->setDescription('Clean up mongoDB reports')
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
        $lock = $factory->createLock(CronLogger::MONGO_CLEAN_UP);

        if ($lock->acquire()) {
            $container = $this->getContainer();
            $logger = $container->get('domain_site.cron.logger');
            $logger->addInfo(CronLogger::MONGO_CLEAN_UP, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

            $rawDataArchivingDate = $this->getRawDataDeletionDate();

            $output->writeln('Start...');

            $output->writeln('Process overview report');
            $this->getBusinessReportManager()->deleteArchivedRawBusinessInteractions($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_CLEAN_UP, CronLogger::STATUS_IN_PROGRESS, 'execute:Process overview report');

            $output->writeln('Process keyword report');
            $this->getKeywordReportManager()->deleteArchivedRawKeywordData($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_CLEAN_UP, CronLogger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

            $output->writeln('Process category report');
            $this->getCategoryReportManager()->deleteArchivedRawCategoryData($rawDataArchivingDate);
            $logger->addInfo(CronLogger::MONGO_CLEAN_UP, CronLogger::STATUS_IN_PROGRESS, 'execute:Process category report');

            $output->writeln('done');
            $logger->addInfo(CronLogger::MONGO_CLEAN_UP, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

            $lock->release();
        } else {
            return $output->writeln('Command is locked by another process');
        }

        return 0;
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
