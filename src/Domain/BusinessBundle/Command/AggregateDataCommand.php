<?php

namespace Domain\BusinessBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\SiteBundle\Logger\CronLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PeriodStatusCommand
 * @package Domain\BusinessBundle\Command
 */
class AggregateDataCommand extends ContainerAwareCommand
{
    private const AGGREGATE_DATA_MONTH_COUNT = '12';

    protected function configure()
    {
        $this
            ->setName('domain:business:aggregate-data')
            ->setDescription('Aggregate impressions, directions, callsMobile, callsDesktop and visits from MongoDB to PostgreSQL')
        ;
    }

    /**
     * @param $input
     * @param $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo(CronLogger::AGGREGATE_DATA_COMMAND, CronLogger::STATUS_START, CronLogger::MESSAGE_START);

        $amountOfUpdatedBusiness = $this->updateBusinessProfilesData($output);

        $logger->addInfo(
            CronLogger::AGGREGATE_DATA_COMMAND,
            CronLogger::STATUS_END . ' updated:' . $amountOfUpdatedBusiness,
            CronLogger::MESSAGE_STOP
        );
    }

    /**
     * @param int $batchSize
     * @param $output
     * @return int
     */
    private function updateBusinessProfilesData(OutputInterface $output)
    {
        $actions = [
            BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON,
            BusinessOverviewModel::TYPE_CODE_IMPRESSION,
            BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON,
            BusinessOverviewModel::TYPE_CODE_CALL_DESK_BUTTON,
            BusinessOverviewModel::TYPE_CODE_VIEW,
        ];

        $entityManager = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $businesses = $entityManager
            ->getRepository(BusinessProfile::class)
            ->getActiveBusinessProfilesIterator();

        $businessesCount = $entityManager
            ->getRepository(BusinessProfile::class)
            ->getActiveBusinessProfilesCount();

        $batchCounter = 0;
        $progressBar = new ProgressBar($output, $businessesCount);
        $progressBar->start();

        foreach ($businesses as $row) {
            /** @var BusinessProfile $business */
            $business = $row[0];
            $businessId = $business->getId();
            $progressBar->advance();
            $data = $this->getBusinessesOverviewData([$businessId], $actions);

            if (isset($data[$businessId])) {
                $businessCursor = $data[$businessId];

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_IMPRESSION])) {
                    $business->setImpressions($businessCursor[BusinessOverviewModel::TYPE_CODE_IMPRESSION]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON])) {
                    $business->setDirections($businessCursor[BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON])) {
                    $business->setCallsMobile($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_DESK_BUTTON])) {
                    $business->setCallsDesktop($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_DESK_BUTTON]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_VIEW])) {
                    $business->setViews($businessCursor[BusinessOverviewModel::TYPE_CODE_VIEW]);
                }

                $batchCounter++;

                $entityManager->flush();
                $entityManager->clear();
            }
        }

        $progressBar->finish();

        return $batchCounter;
    }

    /**
     * @param array $businessesIds
     * @param array $actions
     * @return array
     */
    private function getBusinessesOverviewData($businessesIds, $actions)
    {
        $businessOverviewReportManager = $this
            ->getContainer()
            ->get('domain_report.manager.business_overview_report_manager');

        $endDate = new \DateTime();
        $startDate = new \DateTime();
        $startDate->modify('-' . self::AGGREGATE_DATA_MONTH_COUNT . ' month');

        $data = $businessOverviewReportManager->getSummaryByActionData(
            $businessesIds,
            $actions,
            $startDate,
            $endDate
        );

        return $data;
    }
}
