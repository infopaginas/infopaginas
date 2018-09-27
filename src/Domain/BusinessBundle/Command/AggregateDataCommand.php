<?php

namespace Domain\BusinessBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Model\BusinessOverviewModel;
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
    CONST AGGREGATE_DATA_MONTH_COUNT   = '12';

    protected function configure()
    {
        $this
            ->setName('domain:business:aggregate-data')
            ->setDescription('Aggregate impressions, directions, callsMobile from MongoDB to PostgreSQL')
        ;
    }

    /**
     * @param $input
     * @param $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('domain_site.cron.logger');
        $logger->addInfo($logger::AGGREGATE_DATA_COMMAND, $logger::STATUS_START, 'execute:start');

        $amountOfUpdatedBusiness = $this->updateBusinessProfilesData($output);

        $logger->addInfo(
            $logger::AGGREGATE_DATA_COMMAND,
            $logger::STATUS_END . ' updated:' . $amountOfUpdatedBusiness,
            'execute:stop'
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
        ];

        $entityManager = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $businesses = $entityManager
            ->getRepository(BusinessProfile::class)
            ->getActiveBusinessProfilesIterator();

        $batchCounter = 0;
        $progressBar = new ProgressBar($output, count($businesses));
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
