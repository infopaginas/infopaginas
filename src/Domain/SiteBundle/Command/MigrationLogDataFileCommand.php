<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class MigrationLogDataFileCommand extends ContainerAwareCommand
{
    const CSV_DELIMITER = ',';
    const CSV_MAX_ROW   = 1000;

    const FILE_TYPE_VIEW        = 'visits';
    const FILE_TYPE_IMPRESSIONS = 'impressions';

    const FIELD_VIEW_UID  = 0;
    const FIELD_VIEW_DATE = 1;

    const FIELD_IMPRESSIONS_UID   = 0;
    const FIELD_IMPRESSIONS_DATE  = 1;
    const FIELD_IMPRESSIONS_COUNT = 2;

    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /* @var EntityManager $em */
    protected $em;

    /* @var OutputInterface $output */
    protected $output;

    /* @var bool $withDebug */
    protected $withDebug;

    /* @var string $path */
    protected $path;

    /* @var string $logType */
    protected $logType;

    protected function configure()
    {
        $this->setName('data:log:migrate-old-file');
        $this->setDescription('Migrate old log data file');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('path', 'p', InputOption::VALUE_REQUIRED),
            ])
        );
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

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        if ($input->getOption('path')) {
            $this->path     = $input->getOption('path');
            $this->logType  = $this->getLogType();

            $this->handleLogData();
        } else {
            $this->output->writeln('"path" param is required');
        }
    }

    /**
     * @return bool
     */
    protected function handleLogData()
    {
        switch ($this->logType) {
            case self::FILE_TYPE_VIEW:
                $status = $this->handleViewLogData();
                break;
            case self::FILE_TYPE_IMPRESSIONS:
                $status = $this->handleImpressionLogData();
                break;
            default:
                $status = false;
                break;
        }

        return $status;
    }

    /**
     * @return bool
     */
    protected function handleViewLogData()
    {
        $currentDate = '';
        $logData     = [];

        if (($handle = fopen($this->path, 'r')) !== false) {
            while (($data = fgetcsv($handle, self::CSV_MAX_ROW, self::CSV_DELIMITER)) !== false) {
                $uid        = $data[self::FIELD_VIEW_UID];
                $datetime   = $data[self::FIELD_VIEW_DATE];

                $date = current(explode(' ', $datetime));

                if (!$currentDate) {
                    $currentDate = $date;
                }

                if ($date == $currentDate) {
                    if (!empty($logData[$uid])) {
                        $logData[$uid]++;
                    } else {
                        $logData[$uid] = 1;
                    }

                } else {
                    $this->addViewDataToMongo($logData, $currentDate);
                    $logData = [];
                    $currentDate = $date;
                }
            }

            fclose($handle);
        }

        if ($logData) {
            $this->addViewDataToMongo($logData, $currentDate);
        }
    }

    /**
     * @param array     $logData
     * @param string    $date
     */
    protected function addViewDataToMongo($logData, $date)
    {
        $datetime = \DateTime::createFromFormat(self::DEFAULT_DATE_FORMAT, $date);

        if ($datetime) {
            $datetime->setTime(0, 0, 0);

            $mongoDate = $this->mongoDbManager->typeUTCDateTime($datetime);

            if ($this->logType == self::FILE_TYPE_VIEW) {
                $action = BusinessOverviewModel::TYPE_CODE_VIEW;
            } else {
                $action = BusinessOverviewModel::TYPE_CODE_IMPRESSION;
            }

            $insert = [];
            $i = 0;

            foreach ($logData as $uid => $count) {
                $id = $this->getBusinessIdByUid($uid);

                if ($id) {
                    $insert[] = [
                        BusinessOverviewReportManager::MONGO_DB_FIELD_BUSINESS_ID => $id,
                        BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION      => $action,
                        BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME   => $mongoDate,
                        BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT       => $count,
                    ];

                    if (($i % MongoDbManager::DEFAULT_BATCH_SIZE) === 0) {
                        $this->mongoDbManager->insertMany(
                            BusinessOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                            $insert
                        );
                        $insert = [];
                    }

                    $i++;
                }
            }

            if ($insert) {
                $this->mongoDbManager->insertMany(
                    BusinessOverviewReportManager::MONGO_DB_COLLECTION_NAME_AGGREGATE,
                    $insert
                );
            }

            $this->em->clear();
        }
    }

    /**
     * @return bool
     */
    protected function handleImpressionLogData()
    {
        $currentDate = '';
        $logData     = [];

        if (($handle = fopen($this->path, 'r')) !== false) {
            while (($data = fgetcsv($handle, self::CSV_MAX_ROW, self::CSV_DELIMITER)) !== false) {
                $uid        = $data[self::FIELD_IMPRESSIONS_UID];
                $datetime   = $data[self::FIELD_IMPRESSIONS_DATE];
                $count      = $data[self::FIELD_IMPRESSIONS_COUNT];

                $date = current(explode(' ', $datetime));

                if (!$currentDate) {
                    $currentDate = $date;
                }

                if ($date == $currentDate) {
                    if (!empty($logData[$uid])) {
                        $logData[$uid] += $count;
                    } else {
                        $logData[$uid] = $count;
                    }

                } else {
                    $this->addViewDataToMongo($logData, $currentDate);
                    $logData = [];
                    $currentDate = $date;
                }
            }

            fclose($handle);
        }

        if ($logData) {
            $this->addViewDataToMongo($logData, $currentDate);
        }
    }

    /**
     * @return string
     */
    protected function getLogType()
    {
        $filename = basename ($this->path);

        if (strpos($filename, self::FILE_TYPE_VIEW)) {
            $type = self::FILE_TYPE_VIEW;
        } else {
            $type = self::FILE_TYPE_IMPRESSIONS;
        }

        return $type;
    }

    /**
     * @param string $uid
     *
     * @return int|null
     */
    protected function getBusinessIdByUid($uid)
    {
        $id = null;

        $business = $this->em->createQueryBuilder()
            ->select('b.id')
            ->from(BusinessProfile::class, 'b')
            ->where('b.uid = :uid')
            ->setParameter('uid', $uid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;


        if ($business) {
            $id = current($business);
        }

        return $id;
    }
}
