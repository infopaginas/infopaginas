<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SubscriptionsTrackerCommand
 * @package Domain\ReportBundle\Command
 */
class ReportAggregationCommand extends ContainerAwareCommand
{
    const REPORT_AGGREGATION_RANGE_HOURLY = 'hourly';
    const REPORT_AGGREGATION_RANGE_DAILY  = 'daily';

    protected function configure()
    {
        $this
            ->setName('domain:report-mongo-db:aggregate')
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
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::MONGO_AGGREGATE, $logger::STATUS_START, 'execute:start');

        $period = $this->getAggregationPeriod($input);

        $output->writeln('Start aggregation...');

        $output->writeln('Process overview report');
        $this->getBusinessOverviewReportManager()->aggregateBusinessInteractions($period);
        $logger->addInfo($logger::MONGO_AGGREGATE, $logger::STATUS_IN_PROGRESS, 'execute:Process overview report');

        $output->writeln('Process keyword report');
        $this->getKeywordsReportManager()->aggregateBusinessKeywords($period);
        $logger->addInfo($logger::MONGO_AGGREGATE, $logger::STATUS_IN_PROGRESS, 'execute:Process keyword report');

        $output->writeln('Process category overview report');
        $this->getCategoryOverviewReportReportManager()->aggregateCategoriesInteractions($period);
        $logger->addInfo(
            $logger::MONGO_AGGREGATE,
            $logger::STATUS_IN_PROGRESS,
            'execute:Process category overview report'
        );

        $output->writeln('done');
        $logger->addInfo($logger::MONGO_AGGREGATE, $logger::STATUS_END, 'execute:stop');
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
