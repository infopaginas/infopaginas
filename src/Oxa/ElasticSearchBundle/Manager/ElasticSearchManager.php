<?php

namespace Oxa\ElasticSearchBundle\Manager;

use Elasticsearch;

class ElasticSearchManager
{
    protected $documentIndex;
    protected $indexingPage;
    protected $host;
    protected $documentType = 'default';
    protected $indexRefreshInterval = '30s';
    protected $numberOfShards = 5;
    protected $numberOfReplicas = 1;

    protected $bulkData = [];

    /**
     * @var Elasticsearch\Client
     */
    protected $client;

    public function setConfigData($documentIndex, $indexingPage, $host)
    {
        $this->documentIndex = $documentIndex;
        $this->indexingPage  = $indexingPage;
        $this->host          = $host;
        $builder             = Elasticsearch\ClientBuilder::create()->setHosts([$host]);
        $this->client        = $builder->build();
    }

    public function search($searchQuery)
    {
        $params = [
            'index' => $this->documentIndex,
            'type'  => $this->getDocumentType(),
            'body'  => $searchQuery,
        ];

        $result = $this->client->search($params);

        return $result;
    }

    public function addBulkItems(array $data)
    {
        $this->setIndexPaused();

        try {
            $jsonData = $this->getDefaultBulkJson();

            $indexingPage = $this->indexingPage * 2;

            foreach ($data as $item) {
                $jsonData = $this->addItemToRequest($item, $jsonData);

                if (count($jsonData['body']) >= $indexingPage) {
                    $response = $this->sendBulkData($jsonData);
                    $jsonData = getDefaultBulkJson();
                }
            }

            if (!empty($jsonData['body'])) {
                $response = $this->sendBulkData($jsonData);
            }
        } catch (\Exception $e) {
            //todo error
        }

        //index processing should be enabled
        $this->setIndexProcessing();
        $this->refreshIndex();
    }

    public function createIndex($properties, $sourceEnabled = true)
    {
        $params = [
            'index' => $this->documentIndex,
            'body' => [
                'settings' => [
                    'number_of_shards'   => $this->numberOfShards,
                    'number_of_replicas' => $this->numberOfReplicas,
                    'refresh_interval'   => $this->indexRefreshInterval,
                ],
                'mappings' => [
                    $this->getDocumentType() => [
                        '_source' => [
                            'enabled' => $sourceEnabled, // Source is enabled and all data are saved in original format. Please disable to use lower memory for indexing. You can return index to correct load data in application
                        ],
                        'properties' => $properties,
                    ]
                ]
            ]
        ];

        // Create the index with mappings and settings now
        $response = $this->client->indices()->create($params);
    }

    protected function addItemToRequest(array $data, $jsonData = [])
    {
        if (!$jsonData) {
            $jsonData = $this->getDefaultBulkJson();
        }

        $jsonData['body'][] = json_encode([
            'index' => [
                '_id'   => (int)$data['id'],
                '_index' => $this->documentIndex,
                '_type'  => $this->getDocumentType(),
            ]
        ]);

        $jsonData['body'][] = json_encode($data);

        return $jsonData;
    }

    protected function sendBulkData($jsonData)
    {
        $response = $this->client->bulk($jsonData);

        if (!empty($response['errors'])) {
            // todo error
        }

        return $response;
    }

    protected function getDefaultBulkJson()
    {
        return [
            'index' => $this->documentIndex,
            'type'  => $this->getDocumentType(),
            'body'  => [],
        ];
    }

    protected function setIndexPaused()
    {
        $params = [
            'index' => $this->documentIndex,
            'body' => [
                'settings' => [
                    'refresh_interval'  => -1,
                ],
            ]
        ];

        // Create the index with mappings and settings now
        $response = $this->client->indices()->putSettings($params);
    }

    protected function setIndexProcessing()
    {
        $params = [
            'index' => $this->documentIndex,
            'body' => [
                'settings' => [
                    'refresh_interval'  => $this->indexRefreshInterval,
                ],
            ]
        ];

        // Create the index with mappings and settings now
        $response = $this->client->indices()->putSettings($params);
    }

    protected function refreshIndex()
    {
        $params = [
            'index' => $this->documentIndex,
        ];

        $response = $this->client->indices()->refresh($params);
    }

    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function getIndexingPage()
    {
        return $this->indexingPage;
    }
}
