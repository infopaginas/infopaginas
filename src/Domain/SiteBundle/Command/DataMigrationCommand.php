<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Model\CategoryOverviewModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class DataMigrationCommand extends ContainerAwareCommand
{
    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    protected function configure()
    {
        $this->setName('data:category-overview:migrate-old');
        $this->setDescription('Migrate data from businesses to category');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->mongoDbManager = $this->getContainer()->get('mongodb.manager');
        $this->output = $output;

        $this->migrateBusinessesData($output);
    }

    protected function migrateBusinessesData($output)
    {
        $this->getMongoDBDataFromBusinesses($output);
    }

    private function getMongoDBDataFromBusinesses($output)
    {
        $businessProfileRepository = $this->em
            ->getRepository(BusinessProfile::class);

        $cursor = $this->mongoDbManager->aggregateData(
            BusinessOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION => [
                            '$in' => CategoryOverviewModel::getTypes(),
                        ],
                    ],
                ],
                [
                    '$project' => [
                        'query' => [
                            'bid' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_BUSINESS_ID,
                            'action' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION,
                            'datetime' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME,
                            'count' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT,
                        ],
                    ]
                ],
            ]
        );

        $progressBar = new ProgressBar($output, count($cursor));
        $progressBar->start();
        $categoryIdsArray = [];

        $i = 0;
        $insert = [];

        foreach ($cursor as $document) {
            $document = $document['query'];

            if (isset($document['action']) && isset($document['bid'])) {
                $businessProfileId = $document['bid'];
                $businessProfile = false;

                if (!isset($categoryIdsArray[$businessProfileId])) {
                    /** @var BusinessProfile $businessProfile */
                    $businessProfile = $businessProfileRepository->find($businessProfileId);

                    if ($businessProfile) {
                        $categoryIdsArray[$businessProfileId] = BusinessProfileUtil::extractEntitiesId(
                            $businessProfile->getCategories()->toArray()
                        );
                    }
                }

                if ($businessProfile) {
                    $businessProfileCategories = $categoryIdsArray[$businessProfileId];

                    foreach ($businessProfileCategories as $categoryId) {
                        $newDocument = [
                            CategoryOverviewReportManager::MONGO_DB_FIELD_CATEGORY_ID => $categoryId,
                            CategoryOverviewReportManager::MONGO_DB_FIELD_ACTION => $document['action'],
                            CategoryOverviewReportManager::MONGO_DB_FIELD_DATE_TIME => $document['datetime'],
                            CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT => (int)$document['count'],
                        ];

                        $insert[] = $newDocument;
                    }

                    $i++;

                    if (($i % MongoDbManager::DEFAULT_BATCH_SIZE) === 0) {
                        $this->mongoDbManager->insertMany(
                            CategoryOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                            $insert
                        );
                        $insert = [];
                        $this->em->clear();
                    }

                    $progressBar->advance();
                }
            }
        }

        if ($insert) {
            $this->mongoDbManager->insertMany(
                CategoryOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                $insert
            );
        }

        $progressBar->finish();
    }
}
