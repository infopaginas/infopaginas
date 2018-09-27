<?php

namespace Domain\BusinessBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PeriodStatusCommand
 * @package Domain\BusinessBundle\Command
 */
class AggregateDataCommand extends ContainerAwareCommand
{
    const DEFAULT_BATCH_SIZE = 20;

    CONST AGGREGATE_DATA_MONTH_COUNT   = '12';

    protected function configure()
    {
        $this
            ->setName('domain:business:aggregate-data')
            ->setDescription('Aggregate impressions, directions, callsMobile from MongoDB to PostgreSQL')
            ->addOption(
                'batchSize',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of persisting objects per iteration',
                self::DEFAULT_BATCH_SIZE
            )
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

        $amountOfUpdatedBusiness = $this->updateBusinessProfilesData($input->getOption('batchSize'), $output);

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
    private function updateBusinessProfilesData($batchSize, OutputInterface $output)
    {
        $actions = [
            BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON,
            BusinessOverviewModel::TYPE_CODE_IMPRESSION,
            BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON,
        ];

        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $businesses = $entityManager
            ->getRepository(BusinessProfile::class)
            ->getActiveBusinessesPartial();

        $offset = 0;
        $step = 500;
        $businessesChunks = array_slice($businesses, $offset, $step);

        $progressBar = new ProgressBar($output, count($businesses));
        $progressBar->start();

        while (!empty($businessesChunks)) {
            $businessesIds = BusinessProfileUtil::extractEntitiesId($businessesChunks);

            $data = $this->getBusinessesOverviewData($businessesIds, $actions);
            $batchCounter = 0;

            /** @var BusinessProfile $business */
            foreach ($businessesChunks as $business) {
                $businessId = $business->getId();
                $progressBar->advance();

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

                    $entityManager->merge($business);
                    $batchCounter++;

                    if (($batchCounter % $batchSize) === 0) {
                        $entityManager->flush();
                        $entityManager->clear();
                    }
                }
            }

            $entityManager->flush();
            $entityManager->clear();

            $offset += $step;
            $businessesChunks = array_slice($businesses, $offset, $step);
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
