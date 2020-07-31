<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
 * Class SubscriptionsTrackerCommand
 * @package Domain\ReportBundle\Command
 */
class ReportAggregationCommand extends ContainerAwareCommand
{
    private const REPORT_AGGREGATION_RANGE_HOURLY = 'hourly';
    private const REPORT_AGGREGATION_RANGE_DAILY  = 'daily';

    protected function configure()
    {
        $this
            ->setName(CronLogger::MONGO_AGGREGATE)
            ->setDescription('Aggregate mongoDB reports')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption(
                            'period',
                            'p',
                            InputOption::VALUE_OPTIONAL,
                            'Aggregation period, available options:
                            ' . self::REPORT_AGGREGATION_RANGE_HOURLY . ' - default,
                            ' . self::REPORT_AGGREGATION_RANGE_DAILY
                        ),
                    ]
                )
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $store   = new SemaphoreStore();
        $factory = new Factory($store);
        $lock    = $factory->createLock(CronLogger::MONGO_AGGREGATE);

        if ($lock->acquire()) {
            $logger = $this->getContainer()->get('domain_site.cron.logger');
            $logger->addInfo(CronLogger::MONGO_AGGREGATE, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

            $period = $this->getAggregationPeriod($input);

            $output->writeln('Start aggregation...');

            $output->writeln('Process overview report');
            $this->getBusinessOverviewReportManager()->aggregateBusinessInteractions($period);
            $logger->addInfo(CronLogger::MONGO_AGGREGATE, CronLogger::STATUS_IN_PROGRESS, 'execute:Process overview report');

            $output->writeln('Process keyword report');
            $this->getKeywordsReportManager()->aggregateBusinessKeywords($period);
            $logger->addInfo(CronLogger::MONGO_AGGREGATE, CronLogger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

            $output->writeln('Process category overview report');
            $this->getCategoryOverviewReportReportManager()->aggregateCategoriesInteractions($period);
            $logger->addInfo(
                CronLogger::MONGO_AGGREGATE,
                CronLogger::STATUS_IN_PROGRESS,
                'execute:Process category overview report'
            );

            $output->writeln('done');
            $logger->addInfo(CronLogger::MONGO_AGGREGATE, CronLogger::STATUS_END, CronLogger::MESSAGE_STOP);

            $lock->release();
        } else {
            return $output->writeln('Command is locked by another process');
        }

        return 0;
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
     * @return CategoryOverviewReportManager
     */
    protected function getCategoryOverviewReportReportManager()
    {
        return $this->getContainer()->get('domain_report.manager.category_overview_report_manager');
    }

    /**
     * @param InputInterface $input
     *
     * @return ReportDatesRangeVO
     */
    protected function getAggregationPeriod(InputInterface $input)
    {
        if ($input->getOption('period')) {
            switch ($input->getOption('period')) {
                case self::REPORT_AGGREGATION_RANGE_HOURLY:
                    $period = DatesUtil::RANGE_LAST_HOUR;
                    break;
                case self::REPORT_AGGREGATION_RANGE_DAILY:
                    $period = DatesUtil::RANGE_YESTERDAY;
                    break;
                default:
                    $period = DatesUtil::RANGE_LAST_HOUR;
                    break;
            }
        } else {
            $period = DatesUtil::RANGE_LAST_HOUR;
        }

        $dates = DatesUtil::getDateRangeValueObjectFromRangeType($period);

        return $dates;
    }
}
