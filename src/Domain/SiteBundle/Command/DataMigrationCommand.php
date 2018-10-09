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
    const DIRECTORY = '/../web/uploads/legacyLogs/';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    /* @var bool $withDebug */
    protected $withDebug;

    /* @var string $allowedFileType */
    protected $allowedFileType = 'file';

    /* @var array $allowedFileExtensions */
    protected $allowedFileExtensions = [
        'csv',
    ];

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
        $businessProfileRepository = $this
            ->getContainer()
            ->get('domain_business.manager.business_profile')
            ->getRepository();

        $cursor = $this->mongoDbManager->aggregateData(
            BusinessOverviewReportManager::MONGO_DB_COLLECTION_NAME_RAW,
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
                            'action' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION,
                            'business_id' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_BUSINESS_ID,
                            'datetime' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME,
                        ],
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$query',
                        CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT => [
                            '$sum' => 1,
                        ],
                    ],
                ]
            ]
        );

        $progressBar = new ProgressBar($output, count($cursor));
        $progressBar->start();
        $categoryIdsArray = [];

        $i = 0;
        $insert = [];

        foreach ($cursor as $document) {
            $businessProfileId = $document['_id']['business_id'];
            $businessProfile = false;

            if (isset($document['_id']['action'])) {
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
                        $newDocument[CategoryOverviewReportManager::MONGO_DB_FIELD_CATEGORY_ID] = $categoryId;
                        $newDocument[CategoryOverviewReportManager::MONGO_DB_FIELD_ACTION] = $document['_id']['action'];
                        $newDocument[CategoryOverviewReportManager::MONGO_DB_FIELD_DATE_TIME] = $document['_id']['datetime'];
                        $newDocument[CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT] =
                            (int)$document[CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT];
                        $insert[] = $newDocument;
                    }

                    $i++;

                    if (($i % MongoDbManager::DEFAULT_BATCH_SIZE) === 0) {
                        $this->mongoDbManager->insertMany(
                            CategoryOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                            $insert
                        );
                        $insert = [];
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
