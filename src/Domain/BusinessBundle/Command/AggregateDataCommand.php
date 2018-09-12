<?php

namespace Domain\BusinessBundle\Command;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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

    protected function configure()
    {
        $this
            ->setName('domain:business:aggregate-data')
            ->setDescription('Aggregate data from MongoDB to PostgreSQL')
            ->addOption('batchSize', null, InputOption::VALUE_OPTIONAL, '', self::DEFAULT_BATCH_SIZE)
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

        $amountOfUpdatedBusiness = $this->updateBusinessProfilesData($input->getOption('batchSize'));

        $logger->addInfo(
            $logger::AGGREGATE_DATA_COMMAND,
            $logger::STATUS_END . ' updated:' . $amountOfUpdatedBusiness,
            'execute:stop'
        );
    }

    /**
     * @param int $batchSize
     *
     * @return int
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function updateBusinessProfilesData($batchSize)
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
            ->getActiveBusinessesPartial();

        $businessesIds = BusinessProfileUtil::extractEntitiesId($businesses);

        $cursor = $this->getBusinessesOverviewData(
            $businessesIds,
            $actions
        );

        $batchCounter = 0;

        foreach ($businesses as $business) {
            $businessId = $business->getId();

            if (isset($cursor[$businessId])) {
                $businessCursor = $cursor[$businessId];

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_IMPRESSION])) {
                    $business->setImpressions($businessCursor[BusinessOverviewModel::TYPE_CODE_IMPRESSION]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON])) {
                    $business->setDirections($businessCursor[BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON]);
                }

                if (isset($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON])) {
                    $business->setCallsMobile($businessCursor[BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON]);
                }

                $entityManager->persist($business);
                $batchCounter++;

                if (($batchCounter % $batchSize) === 0) {
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }
        }

        $entityManager->flush();
        $entityManager->clear();

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
        $startDate = clone $endDate;
        $startDate->modify('-' . BusinessProfile::AGGREGATE_DATA_MONTH_COUNT . ' month');

        $cursor = $businessOverviewReportManager->getSummaryByActionData(
            $businessesIds,
            $actions,
            $startDate,
            $endDate
        );

        return $cursor;
    }
}
