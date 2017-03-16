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
        try {
            $result = $this->client->$collectionName->insertOne($data);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param       $collectionName
     * @param array $data
     *
     * @return mixed
     */
    public function insertMany($collectionName, $data)
    {
        try {
            $result = $this->client->$collectionName->insertMany($data);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param       $collectionName
     * @param array $query
     * @param array $options
     *
     * @return mixed
     */
    public function find($collectionName, $query, $options = [])
    {
        try {
            $result = $this->client->$collectionName->find($query, $options);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param       $collectionName
     * @param array $data
     *
     * @return mixed
     */
    public function createIndex($collectionName, array $data)
    {
        try {
            $result = $this->client->$collectionName->createIndex($data);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $collectionName
     * @param $indexName
     *
     * @return mixed
     */
    public function dropIndex($collectionName, $indexName)
    {
        try {
            $result = $this->client->$collectionName->dropIndex($indexName);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $collectionName
     * @param array  $options
     *
     * @return mixed
     */
    public function aggregateData($collectionName, array $options)
    {
        try {
            $result = $this->client->$collectionName->aggregate($options);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
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
     * @return MongoDB\BSON\ObjectId
     */
    public function generateId()
    {
        return new MongoDB\BSON\ObjectId();
    }
}
