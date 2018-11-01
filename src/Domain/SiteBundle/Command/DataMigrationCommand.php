<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
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

    const CATEGORIES = 'categories';
    const CATALOG_LOCALITY = 'catalogLocality';

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

        $this->migrateBusinessesData($output);
    }

    protected function migrateBusinessesData($output)
    {
        $this->getMongoDBDataFromBusinesses($output);
        $this->getMongoDBDataFromCategories($output);
    }

    private function getMongoDBDataFromBusinesses($output)
    {
        $businessProfileRepository = $this->em
            ->getRepository(BusinessProfile::class);

        $cursor = $this->mongoDbManager->find(
            BusinessOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION => [
                    '$nin' => [
                        BusinessOverviewModel::TYPE_CODE_VIEW,
                        BusinessOverviewModel::TYPE_CODE_IMPRESSION,
                    ],
                ],
            ]
        );

        $progressBar = new ProgressBar($output, count($cursor));
        $progressBar->start();
        $categoryIdsArray = [];

        $i = 0;
        $insert = [];
        if ($cursor) {
            foreach ($cursor as $document) {
                if (isset($document['action']) && isset($document['business_id'])) {
                    $businessProfileId = $document['business_id'];

                    if (!isset($categoryIdsArray[$businessProfileId])) {
                        /** @var BusinessProfile $businessProfile */
                        $businessProfile = $businessProfileRepository->find($businessProfileId);

                        if ($businessProfile) {
                            $businessProfileCategories = BusinessProfileUtil::extractEntitiesId(
                                $businessProfile->getCategories()->toArray()
                            );

                            $catalogLocality = $businessProfile->getCatalogLocality();

                            if ($catalogLocality && $businessProfileCategories) {
                                $categoryIdsArray[$businessProfileId][self::CATEGORIES] = $businessProfileCategories;
                                $categoryIdsArray[$businessProfileId][self::CATALOG_LOCALITY] = $catalogLocality->getId();
                            }
                        }
                    }

                    if (isset($categoryIdsArray[$businessProfileId])) {
                        $businessProfileData = $categoryIdsArray[$businessProfileId];

                        foreach ($businessProfileData[self::CATEGORIES] as $categoryId) {
                            $newDocument = [
                                CategoryOverviewReportManager::MONGO_DB_FIELD_CATEGORY_ID => $categoryId,
                                CategoryOverviewReportManager::MONGO_DB_FIELD_ACTION => $document['action'],
                                CategoryOverviewReportManager::MONGO_DB_FIELD_LOCALITY_ID =>
                                    $businessProfileData[self::CATALOG_LOCALITY],
                                CategoryOverviewReportManager::MONGO_DB_FIELD_TYPE =>
                                    BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS,
                                CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT => (int)$document['count'],
                                CategoryOverviewReportManager::MONGO_DB_FIELD_DATE_TIME => $document['datetime'],
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
        }

        if ($insert) {
            $this->mongoDbManager->insertMany(
                CategoryOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                $insert
            );
        }

        $progressBar->finish();
    }

    private function getMongoDBDataFromCategories($output)
    {
        $cursor = $this->mongoDbManager->find(
            CategoryReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
            ]
        );

        $progressBar = new ProgressBar($output, count($cursor));
        $progressBar->start();

        $i = 0;
        $insert = [];

        if ($cursor) {
            foreach ($cursor as $document) {
                if (isset($document['locality_id']) && isset($document['type'])) {

                    $newDocument = [
                        CategoryOverviewReportManager::MONGO_DB_FIELD_CATEGORY_ID => $document['category_id'],
                        CategoryOverviewReportManager::MONGO_DB_FIELD_ACTION => BusinessOverviewModel::TYPE_CODE_IMPRESSION,
                        CategoryOverviewReportManager::MONGO_DB_FIELD_LOCALITY_ID => $document['locality_id'],
                        CategoryOverviewReportManager::MONGO_DB_FIELD_TYPE => $document['type'],
                        CategoryOverviewReportManager::MONGO_DB_FIELD_COUNT => (int)$document['count'],
                        CategoryOverviewReportManager::MONGO_DB_FIELD_DATE_TIME => $document['datetime'],
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

        if ($insert) {
            $this->mongoDbManager->insertMany(
                CategoryOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                $insert
            );
        }

        $progressBar->finish();
    }
}
