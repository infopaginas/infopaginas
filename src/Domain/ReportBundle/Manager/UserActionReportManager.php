<?php

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Model\ExporterInterface;
use Domain\ReportBundle\Model\UserActionModel;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserActionReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME          = 'user_action';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE  = 'user_action_archive';

    const MONGO_DB_FIELD_USER_NAME     = 'user_name';
    const MONGO_DB_FIELD_USER_ID       = 'user_id';
    const MONGO_DB_FIELD_DATE_TIME     = 'datetime';
    const MONGO_DB_FIELD_ENTITY        = 'entity';
    const MONGO_DB_FIELD_ENTITY_SEARCH = 'entity_search';
    const MONGO_DB_FIELD_ENTITY_NAME   = 'entity_name';
    const MONGO_DB_FIELD_ENTITY_NAME_SEARCH = 'entity_name_search';
    const MONGO_DB_FIELD_ACTION        = 'action';
    const MONGO_DB_FIELD_DATA          = 'data';
    const MONGO_DB_FIELD_DATA_BEFORE   = 'data_before';
    const MONGO_DB_FIELD_DATA_AFTER    = 'data_after';

    const MONGO_DB_DEFAULT_USER      = 'unknown';
    const MONGO_DB_DEFAULT_USER_ID   = 0;

    protected $reportName = 'user_action_report';

    /** @var  TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /**
     * @param TokenStorage $tokenStorage
     * @param MongoDbManager $mongoDbManager
     */
    public function __construct(TokenStorage $tokenStorage, MongoDbManager $mongoDbManager)
    {
        $this->tokenStorage     = $tokenStorage;
        $this->mongoDbManager   = $mongoDbManager;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getUserActionReportData(array $params = [])
    {
        $userActionRawResult = $this->getUserActionsData($params);
        $total = $this->countUserActionsData($params);

        $result = $this->prepareUserActionReportStats($userActionRawResult);

        $currentPage = $params['_page'];
        $lastPage = ceil($total / $params['_per_page']);
        $nextPage = $lastPage;
        $previousPage = 1;

        if ($currentPage + 1 < $lastPage) {
            $nextPage = $currentPage + 1;
        }

        if ($currentPage - 1 > 1) {
            $previousPage = $currentPage - 1;
        }

        $rangePage = AdminHelper::getPageRanges($currentPage, $lastPage);

        $result['currentPage']  = $currentPage;
        $result['rangePage']    = $rangePage;
        $result['lastPage']     = $lastPage;
        $result['nextPage']     = $nextPage;
        $result['previousPage'] = $previousPage;
        $result['perPage']      = $params['_per_page'];
        $result['total']        = $total;

        return $result;
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function getUserActionReportExportDataIterator($parameters)
    {
        $userActionCursor = $this->getUserActionsExportData($parameters);

        return $userActionCursor;
    }

    /**
     * @param array
     *
     * @return array
     */
    public function convertMongoDataToArray($rawData)
    {
        $mapping = self::getUserActionReportMapping();
        $result  = [];

        foreach ($mapping as $key => $value) {
            if (array_key_exists($key, $rawData)) {
                switch ($key) {
                    case self::MONGO_DB_FIELD_DATE_TIME:
                        $value = DatesUtil::convertMongoDbTimeToDatetime($rawData[self::MONGO_DB_FIELD_DATE_TIME])
                            ->format(AdminHelper::DATETIME_FORMAT);
                        break;
                    case self::MONGO_DB_FIELD_DATA:
                        $value = $rawData[self::MONGO_DB_FIELD_DATA]->getArrayCopy();
                        break;
                    case self::MONGO_DB_FIELD_DATA_BEFORE:
                        $value = $rawData[self::MONGO_DB_FIELD_DATA_BEFORE]->getArrayCopy();
                        break;
                    case self::MONGO_DB_FIELD_DATA_AFTER:
                        $value = $rawData[self::MONGO_DB_FIELD_DATA_AFTER]->getArrayCopy();
                        break;
                    default:
                        $value = $rawData[$key];
                        break;
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param mixed $rawResult
     *
     * @return array
     */
    protected function prepareUserActionReportStats($rawResult) : array
    {
        $mapping = self::getUserActionReportMapping();
        $results = [];

        foreach ($rawResult as $rowKey => $item) {
            foreach ($mapping as $key => $value) {
                if (array_key_exists($key, $item)) {
                    switch ($key) {
                        case self::MONGO_DB_FIELD_DATE_TIME:
                            $value = DatesUtil::convertMongoDbTimeToDatetime($item[self::MONGO_DB_FIELD_DATE_TIME])
                                ->format(AdminHelper::DATETIME_FORMAT);
                            break;
                        case self::MONGO_DB_FIELD_DATA:
                            $value = $item[self::MONGO_DB_FIELD_DATA]->getArrayCopy();
                            break;
                        case self::MONGO_DB_FIELD_DATA_BEFORE:
                            $value = $item[self::MONGO_DB_FIELD_DATA_BEFORE]->getArrayCopy();
                            break;
                        case self::MONGO_DB_FIELD_DATA_AFTER:
                            $value = $item[self::MONGO_DB_FIELD_DATA_AFTER]->getArrayCopy();
                            break;
                        default:
                            $value = $item[$key];
                            break;
                    }

                    $results[$rowKey][$key] = $value;
                }
            }
        }

        return [
            'mapping' => $mapping,
            'results' => $results,
        ];
    }

    /**
     * @param string $action
     * @param array  $data
     *
     * @return bool
     */
    public function registerUserAction($action, $data = [])
    {
        if (!in_array($action, UserActionModel::getTypes())) {
            return false;
        }

        $data = $this->buildUserAction($action, $data);
        $this->insertUserAction($data);

        return true;
    }

    /**
     * @param string $action
     * @param array  $data
     *
     * @return array
     */
    protected function buildUserAction($action, $data = [])
    {
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        if ($this->tokenStorage->getToken() !== null and $this->tokenStorage->getToken()->getUser() instanceof User) {
            $currentUser = $this->tokenStorage->getToken()->getUser();
            $userName   = $currentUser->getFullname();
            $userId     = $currentUser->getId();
        } else {
            $userName = self::MONGO_DB_DEFAULT_USER;
            $userId   = self::MONGO_DB_DEFAULT_USER_ID;
        }

        if (empty($data['entityName'])) {
            $data['entityName'] = '';
        }

        $dataSet = [];

        if (!empty($data['dataSet'])) {
            $dataSet = $data['dataSet'];
        }

        unset($data['dataSet']);

        $userAction = [
            self::MONGO_DB_FIELD_USER_NAME      => $userName,
            self::MONGO_DB_FIELD_USER_ID        => $userId,
            self::MONGO_DB_FIELD_DATE_TIME      => $date,
            self::MONGO_DB_FIELD_ENTITY         => $data['entity'],
            self::MONGO_DB_FIELD_ENTITY_SEARCH  => AdminHelper::convertAccentedString($data['entity']),
            self::MONGO_DB_FIELD_ENTITY_NAME    => $data['entityName'],
            self::MONGO_DB_FIELD_ENTITY_NAME_SEARCH => AdminHelper::convertAccentedString($data['entityName']),
            self::MONGO_DB_FIELD_ACTION         => $action,
            self::MONGO_DB_FIELD_DATA           => $data,
            self::MONGO_DB_FIELD_DATA_BEFORE    => $dataSet ? ($dataSet['dataBefore']) : [],
            self::MONGO_DB_FIELD_DATA_AFTER     => $dataSet ? ($dataSet['dataAfter'])  : [],
        ];

        return $userAction;
    }

    /**
     * @param array $data
     */
    protected function insertUserAction($data)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME, [
            self::MONGO_DB_FIELD_USER_ID    => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_ACTION     => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_ENTITY_SEARCH      => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_ENTITY_NAME_SEARCH => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_DATE_TIME  => MongoDbManager::INDEX_TYPE_DESC,
        ]);

        $this->mongoDbManager->insertOne(
            self::MONGO_DB_COLLECTION_NAME,
            $data
        );
    }

    /**
     * @param \Datetime $date
     */
    public function archiveUserActions($date)
    {
        $this->mongoDbManager->archiveCollection(
            self::MONGO_DB_COLLECTION_NAME,
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getUserActionsData($params)
    {
        $cursor = $this->mongoDbManager->find(
            self::MONGO_DB_COLLECTION_NAME,
            $this->getMongoSearchQuery($params),
            [
                'skip'  => (int) ($params['_per_page'] * ($params['_page'] - 1)),
                'limit' => (int) $params['_per_page'],
                'sort'  => [
                    self::MONGO_DB_FIELD_DATE_TIME => MongoDbManager::INDEX_TYPE_DESC,
                ],
            ]
        );

        return $cursor;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getUserActionsExportData($params)
    {
        $cursor = $this->mongoDbManager->find(
            self::MONGO_DB_COLLECTION_NAME,
            $this->getMongoSearchQuery($params),
            [
                'sort'  => [
                    self::MONGO_DB_FIELD_DATE_TIME => MongoDbManager::INDEX_TYPE_DESC,
                ],
            ]
        );

        return $cursor;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function countUserActionsData($params)
    {
        $count = $this->mongoDbManager->count(
            self::MONGO_DB_COLLECTION_NAME,
            $this->getMongoSearchQuery($params)
        );

        return $count;
    }

    /**
     * @return array
     */
    public static function getUserActionReportMapping()
    {
        return [
            self::MONGO_DB_FIELD_USER_NAME   => 'user_action_report.mapping.user_name',
            self::MONGO_DB_FIELD_USER_ID     => 'user_action_report.mapping.user_id',
            self::MONGO_DB_FIELD_DATE_TIME   => 'user_action_report.mapping.datetime',
            self::MONGO_DB_FIELD_ENTITY      => 'user_action_report.mapping.entity',
            self::MONGO_DB_FIELD_ENTITY_NAME => 'user_action_report.mapping.entity_name',
            self::MONGO_DB_FIELD_ACTION      => 'user_action_report.mapping.action',
            self::MONGO_DB_FIELD_DATA        => 'user_action_report.mapping.data',
            self::MONGO_DB_FIELD_DATA_BEFORE => 'user_action_report.mapping.data_before',
            self::MONGO_DB_FIELD_DATA_AFTER  => 'user_action_report.mapping.data_after',
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function getMongoSearchQuery($params)
    {
        $query = [];

        if (!empty($params['username']['value'])) {
            $query[self::MONGO_DB_FIELD_USER_ID] = (int) $params['username']['value'];
        }

        if (!empty($params['action']['value'])) {
            $query[self::MONGO_DB_FIELD_ACTION] = $params['action']['value'];
        }

        if (!empty($params['entity']['value'])) {
            $entitySearch = AdminHelper::convertAccentedString($params['entity']['value']);

            $query[self::MONGO_DB_FIELD_ENTITY_SEARCH] = $this->mongoDbManager->typeRegularExpression($entitySearch);
        }

        if (!empty($params['entityName']['value'])) {
            $entityName = AdminHelper::convertAccentedString($params['entityName']['value']);

            $query[self::MONGO_DB_FIELD_ENTITY_NAME_SEARCH] = $this->mongoDbManager->typeRegularExpression($entityName);
        }

        $datetime = [];

        if (!empty($params['date']['value']['start'])) {
            $start = \DateTime::createFromFormat(AdminHelper::FILTER_DATE_FORMAT, $params['date']['value']['start']);

            if ($start) {
                $start = DatesUtil::setDayStart($start);
                $datetime['$gte'] = $this->mongoDbManager->typeUTCDateTime($start);
            }
        }

        if (!empty($params['date']['value']['end'])) {
            $end = \DateTime::createFromFormat(AdminHelper::FILTER_DATE_FORMAT, $params['date']['value']['end']);

            if ($end) {
                $end = DatesUtil::setDayEnd($end);
                $datetime['$lte'] = $this->mongoDbManager->typeUTCDateTime($end);
            }
        }

        if ($datetime) {
            $query[self::MONGO_DB_FIELD_DATE_TIME] = $datetime;
        }

        return $query;
    }
}
