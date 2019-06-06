<?php

namespace Domain\ReportBundle\Manager;

use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class FeedbackReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME          = 'feedback';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE  = 'feedback_archive';

    const MONGO_DB_FIELD_FULL_NAME     = 'full_name';
    const MONGO_DB_FIELD_BUSINESS_NAME = 'business_name';
    const MONGO_DB_FIELD_PHONE         = 'phone';
    const MONGO_DB_FIELD_EMAIL         = 'email';
    const MONGO_DB_FIELD_SUBJECT       = 'subject';
    const MONGO_DB_FIELD_MESSAGE       = 'message';
    const MONGO_DB_FIELD_LOCALE        = 'locale';
    const MONGO_DB_FIELD_USER_ID       = 'user_id';
    const MONGO_DB_FIELD_DATE_TIME     = 'datetime';

    const MONGO_DB_FIELD_FULL_NAME_SEARCH       = 'full_name_search';
    const MONGO_DB_FIELD_BUSINESS_NAME_SEARCH   = 'business_name_search';
    const MONGO_DB_FIELD_MESSAGE_SEARCH         = 'message_search';

    const MONGO_DB_DEFAULT_USER_ID   = 0;

    const MAX_LENGTH_FULL_NAME      = 256;
    const MAX_LENGTH_BUSINESS_NAME  = 256;
    const MAX_LENGTH_EMAIL          = 256;
    const MAX_LENGTH_MESSAGE        = 1500;

    protected $reportName = 'feedback_report';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /** @var Mailer $mailer */
    protected $mailer;

    /**
     * @param MongoDbManager $mongoDbManager
     */
    public function __construct(MongoDbManager $mongoDbManager, Mailer $mailer)
    {
        $this->mongoDbManager   = $mongoDbManager;
        $this->mailer           = $mailer;
    }

    /**
     * @param array $data
     */
    public function handleFeedback($data)
    {
        if ($data['isReportProblem']) {
            $data['subject'] = Page::SUBJECT_REPORT_A_PROBLEM;

            $this->mailer->sendReportProblemEmailMessage($data);
        } else {
            $this->mailer->sendFeedbackEmailMessage($data);
        }

        $this->registerFeedback($data);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getFeedbackReportData(array $params = [])
    {
        $feedbackRawResult = $this->getFeedbackData($params);
        $total = $this->countFeedbackData($params);

        $result = $this->prepareFeedbackReportStats($feedbackRawResult);

        $currentPage = $params['_page'];
        $lastPage = ceil($total / $params['_per_page']);
        $nextPage = $lastPage;
        $previousPage = 1;

        if (($currentPage + 1) < $lastPage) {
            $nextPage = $currentPage + 1;
        }

        if (($currentPage - 1) > 1) {
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
     * @param mixed $rawResult
     *
     * @return array
     */
    protected function prepareFeedbackReportStats($rawResult) : array
    {
        $mapping = self::getFeedbackReportMapping();
        $results = [];

        foreach ($rawResult as $rowKey => $item) {
            foreach ($mapping as $key => $value) {
                if (array_key_exists($key, $item)) {
                    switch ($key) {
                        case self::MONGO_DB_FIELD_DATE_TIME:
                            $value = DatesUtil::convertMongoDbTimeToDatetime($item[self::MONGO_DB_FIELD_DATE_TIME])
                                ->format(AdminHelper::DATETIME_FORMAT);
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
     * @param array $data
     *
     * @return bool
     */
    public function registerFeedback($data)
    {
        $data = $this->buildFeedbackAction($data);

        $this->insertFeedbackAction($data);

        return true;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function buildFeedbackAction($data)
    {
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        $userAction = [
            self::MONGO_DB_FIELD_FULL_NAME      => $data['fullName'],
            self::MONGO_DB_FIELD_BUSINESS_NAME  => $data['businessName'],
            self::MONGO_DB_FIELD_PHONE          => $data['phone'],
            self::MONGO_DB_FIELD_EMAIL          => $data['email'],
            self::MONGO_DB_FIELD_SUBJECT        => $data['subject'],
            self::MONGO_DB_FIELD_MESSAGE        => $data['message'],
            self::MONGO_DB_FIELD_LOCALE         => $data['locale'],
            self::MONGO_DB_FIELD_USER_ID        => $data['userId'],
            self::MONGO_DB_FIELD_DATE_TIME      => $date,
            self::MONGO_DB_FIELD_FULL_NAME_SEARCH       => AdminHelper::convertAccentedString($data['fullName']),
            self::MONGO_DB_FIELD_BUSINESS_NAME_SEARCH   => AdminHelper::convertAccentedString($data['businessName']),
            self::MONGO_DB_FIELD_MESSAGE_SEARCH         => AdminHelper::convertAccentedString($data['message']),
        ];

        return $userAction;
    }

    /**
     * @param array $data
     */
    protected function insertFeedbackAction($data)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME, [
            self::MONGO_DB_FIELD_DATE_TIME => MongoDbManager::INDEX_TYPE_DESC,
            self::MONGO_DB_FIELD_LOCALE    => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_SUBJECT   => MongoDbManager::INDEX_TYPE_ASC,
        ]);

        $this->mongoDbManager->insertOne(
            self::MONGO_DB_COLLECTION_NAME,
            $data
        );
    }

    /**
     * @param \Datetime $date
     */
    public function archiveFeedbackActions($date)
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
    public function getFeedbackData($params)
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
    public function countFeedbackData($params)
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
    public static function getFeedbackReportMapping()
    {
        return [
            self::MONGO_DB_FIELD_FULL_NAME      => 'feedback_report.mapping.full_name',
            self::MONGO_DB_FIELD_BUSINESS_NAME  => 'feedback_report.mapping.business_name',
            self::MONGO_DB_FIELD_PHONE          => 'feedback_report.mapping.phone',
            self::MONGO_DB_FIELD_EMAIL          => 'feedback_report.mapping.email',
            self::MONGO_DB_FIELD_SUBJECT        => 'feedback_report.mapping.subject',
            self::MONGO_DB_FIELD_MESSAGE        => 'feedback_report.mapping.message',
            self::MONGO_DB_FIELD_LOCALE         => 'feedback_report.mapping.locale',
            self::MONGO_DB_FIELD_USER_ID        => 'feedback_report.mapping.user_id',
            self::MONGO_DB_FIELD_DATE_TIME      => 'feedback_report.mapping.date',
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

        if (!empty($params['fullName']['value'])) {
            $fullName = AdminHelper::convertAccentedString($params['fullName']['value']);

            $query[self::MONGO_DB_FIELD_FULL_NAME_SEARCH] = $this->mongoDbManager->typeRegularExpression($fullName);
        }

        if (!empty($params['businessName']['value'])) {
            $businessName = AdminHelper::convertAccentedString($params['businessName']['value']);

            $query[self::MONGO_DB_FIELD_BUSINESS_NAME_SEARCH] = $this->mongoDbManager->typeRegularExpression(
                $businessName
            );
        }

        if (!empty($params['phone']['value'])) {
            $query[self::MONGO_DB_FIELD_PHONE] = $this->mongoDbManager->typeRegularExpression(
                $params['phone']['value']
            );
        }

        if (!empty($params['email']['value'])) {
            $query[self::MONGO_DB_FIELD_EMAIL] = $this->mongoDbManager->typeRegularExpression(
                $params['email']['value']
            );
        }

        if (!empty($params['message']['value'])) {
            $message = AdminHelper::convertAccentedString($params['message']['value']);

            $query[self::MONGO_DB_FIELD_MESSAGE_SEARCH] = $this->mongoDbManager->typeRegularExpression($message);
        }

        if (!empty($params['subject']['value'])) {
            $query[self::MONGO_DB_FIELD_SUBJECT] = $params['subject']['value'];
        }

        if (!empty($params['locale']['value'])) {
            $query[self::MONGO_DB_FIELD_LOCALE] = $params['locale']['value'];
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
