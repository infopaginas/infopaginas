<?php

namespace Oxa\ElasticSearchBundle\Manager;

use Elasticsearch;

class ElasticSearchManager
{
    const INDEX_NOT_FOUND_EXCEPTION = 'index_not_found_exception';
    const INDEX_ALREADY_EXISTS_EXCEPTION = 'index_already_exists_exception';

    const AUTO_SUGGEST_BUSINESS_MIN_WORD_LENGTH_ANALYZED = 2;
    const AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED = 10;

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
        $status = true;
        $this->setIndexPaused();

        try {
            $jsonData = $this->getDefaultBulkJson();

            $indexingPage = $this->indexingPage * 2;

            foreach ($data as $item) {
                $jsonData = $this->addItemToRequest($item, $jsonData);

                if (count($jsonData['body']) >= $indexingPage) {
                    $response = $this->sendBulkData($jsonData);
                    $jsonData = $this->getDefaultBulkJson();
                }
            }

            if (!empty($jsonData['body'])) {
                $response = $this->sendBulkData($jsonData);
            }
        } catch (\Exception $e) {
            $status = $e->getMessage();
        }

        //index processing should be enabled
        $this->setIndexProcessing();
        $this->refreshIndex();

        return $status;
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
                    'analysis' => [
                        'analyzer' => [
                            'folding' => [
                                'tokenizer' => 'standard',
                                'filter' =>  [
                                    'lowercase',
                                    'asciifolding'
                                ],
                            ],
                            'autocomplete' => [
                                'tokenizer' => 'autocomplete',
                                'filter' =>  [
                                    'lowercase',
                                ],
                            ],

                            'autocomplete_search' => [
                                'tokenizer' => 'lowercase',
                            ],
                        ],
                        'tokenizer' => [
                            'autocomplete' => [
                                'type' => 'edge_ngram',
                                'min_gram' => self::AUTO_SUGGEST_BUSINESS_MIN_WORD_LENGTH_ANALYZED,
                                'max_gram' => self::AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED,
                                'token_chars' => [
                                    'letter',
                                    'digit',
                                ],
                            ],
                        ],
                    ],
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

        return $response;
    }

    public function deleteItem($id)
    {
        $params = [
            'index' => $this->documentIndex,
            'type'  => $this->getDocumentType(),
            'id'    => (int)$id,
        ];

        $response = $this->client->delete($params);

        return $response;
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
