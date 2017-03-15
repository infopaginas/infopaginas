<?php

namespace Domain\ReportBundle\Command;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
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
            );
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $period = $this->getAggregationPeriod($input);

        $output->writeln('Start aggregation...');

        $output->writeln('Process overview report');
        $this->getBusinessOverviewReportManager()->aggregateBusinessInteractions($period);

        $output->writeln('Process keyword report');
        $this->getKeywordsReportManager()->aggregateBusinessKeywords($period);

        $output->writeln('done');
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