<?php

namespace Oxa\MongoDbBundle\Manager;

use MongoDB;
use MongoDB\BSON\UTCDateTime;

class MongoDbManager
{
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
    public function insertOne($collectionName, array $data)
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
     * @param array $options
     *
     *
     */
    public function aggregateData(array $options, $collectionName)
    {
        $cursor = $this->client->$collectionName->aggregate($options);

        /*

        Controller:

        $mongoDbManager = $this->get('mongodb.manager');
        $mongoDbManager->createIndex('test_4', ['name' => 1]);
        $mongoDbManager->insertOne('test_4', [
            'name'     => 'name_1',
            'datetime' => $mongoDbManager->typeUTCDateTime(new \DateTime())
        ]);

        -----------
        Example:

        ['$match' =>
            [
                'name' => [
                    '$not'    => ['$type' => 10],
                    '$exists' => true,
                ],
            ],
        ],
        ['$group' => ['_id' => '$word', 'count' => ['$sum' => 1]]],
        ['$sort' => ['datetime' => 1]]


        see: https://docs.mongodb.com/php-library/master/tutorial/collation/#aggregation
        */

        // TODO: move out
        foreach ($cursor as $document) {
            $document['_id'] = $this->gerenateId();
            $document['datetime'] = $this->typeUTCDateTime(new \DateTime());
            $this->insertOne($collectionName, $document)->insertOne($document);
        }
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
