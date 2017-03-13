<?php

namespace Oxa\MongoDbBundle\Manager;

use MongoDB;
use MongoDB\BSON\UTCDateTime;

class MongoDbManager
{
    const AGGREGATE_FORMAT_DAILY = 'Y-m-d';
    const DEFAULT_TIME_ZONE      = 'UTC';

    /**
     * @var MongoDB\Client
     */
    protected $client;

    /**
     * @param string $db
     * @param string $host
     */
    public function setConfigData($db, $host)
    {
        $this->db     = $db;
        $this->host   = $host;
        $this->client = (new MongoDB\Client($host))->$db;
    }

    /**
     * @param       $collectionName
     * @param array $data
     *
     * @return mixed
     */
    public function insertOne($collectionName, $data)
    {
        return $this->client->$collectionName->insertOne($data);
    }

    /**
     * @param       $collectionName
     * @param array $data
     *
     * @return mixed
     */
    public function createIndex($collectionName, array $data)
    {
        return $this->client->$collectionName->createIndex($data);
    }

    /**
     * @param $collectionName
     * @param $indexName
     *
     * @return mixed
     */
    public function dropIndex($collectionName, $indexName)
    {
        return $this->client->$collectionName->dropIndex($indexName);
    }

    /**
     * @param string $collectionName
     * @param array  $options
     */
    public function aggregateData($collectionName, array $options)
    {
        $cursor = $this->client->$collectionName->aggregate($options);

        return $cursor;
    }

    /**
     * @param \DateTime $datetime
     *
     * @return UTCDateTime
     */
    public function typeUTCDateTime(\DateTime $datetime)
    {
        return new MongoDB\BSON\UTCDateTime($datetime);
    }

    /**
     * @return UTCDateTime
     */
    public function typeUTCDateToday()
    {
        $today = new \DateTime(
            date(self::AGGREGATE_FORMAT_DAILY),
            new \DateTimeZone(self::DEFAULT_TIME_ZONE)
        );

        return $this->typeUTCDateTime($today);
    }

    /**
     * @return MongoDB\BSON\ObjectId
     */
    public function generateId()
    {
        return new MongoDB\BSON\ObjectId();
    }
}
