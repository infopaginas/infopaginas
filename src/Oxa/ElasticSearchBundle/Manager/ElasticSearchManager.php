<?php

namespace Oxa\ElasticSearchBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Manager\LocalityManager;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Elasticsearch;
use Psr\Log\LoggerInterface;

class ElasticSearchManager
{
    public const INDEX_NOT_FOUND_EXCEPTION = 'not_found';
    public const INDEX_NOT_FOUND_STATUS_CODE = 404;
    public const INDEX_ALREADY_EXISTS_EXCEPTION = 'resource_already_exists_exception';

    public const AUTO_SUGGEST_BUSINESS_MIN_WORD_LENGTH_ANALYZED = 2;
    public const AUTO_SUGGEST_BUSINESS_MAX_WORD_LENGTH_ANALYZED = 40;
    public const BUSINESS_PHONE_MIN_WORD_LENGTH_ANALYZED = 7;
    public const BUSINESS_PHONE_MAX_WORD_LENGTH_ANALYZED = 10;
    public const MAX_NGRAM_DIFF = self::BUSINESS_PHONE_MAX_WORD_LENGTH_ANALYZED - self::BUSINESS_PHONE_MIN_WORD_LENGTH_ANALYZED;
    public const MILES_IN_METER = 0.000621371;

    public const ELASTIC_INDEXES = [
        BusinessProfile::ELASTIC_INDEX,
        BusinessProfile::ELASTIC_INDEX_AD,
        Locality::ELASTIC_INDEX,
        Category::ELASTIC_INDEX,
        EmergencyBusiness::ELASTIC_INDEX,
    ];

    protected $indexingPage;
    protected $host;
    protected $indexRefreshInterval = '30s';
    protected $numberOfShards = 1;
    protected $numberOfReplicas = 0;

    // see https://www.elastic.co/guide/en/elasticsearch/reference/current/index-modules.html
    protected $maxResultWindow  = 1000000;
    protected $maxRescoreWindow = 1000000;

    protected $logger;

    /**
     * @var Elasticsearch\Client
     */
    protected $client;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getElasticMappings(): array
    {
        return [
            BusinessProfile::ELASTIC_INDEX => BusinessProfileManager::getBusinessElasticSearchIndexParams(),
            BusinessProfile::ELASTIC_INDEX_AD => BusinessProfileManager::getBusinessAdElasticSearchIndexParams(),
            Locality::ELASTIC_INDEX => LocalityManager::getLocalityElasticSearchIndexParams(),
            Category::ELASTIC_INDEX => CategoryManager::getCategoryElasticSearchIndexParams(),
            EmergencyBusiness::ELASTIC_INDEX => EmergencyManager::getEmergencyBusinessElasticSearchIndexParams(),
        ];
    }

    public function setConfigData($indexingPage, $host): void
    {
        $this->indexingPage  = $indexingPage;
        $this->host          = $host;
        $builder             = Elasticsearch\ClientBuilder::create()->setHosts([$host]);
        $this->client        = $builder->build();
    }

    public function search(string $index, $searchQuery)
    {
        $params = [
            'index' => $index,
            'body'  => $searchQuery,
        ];

        return $this->client->search($params);
    }

    public function addBulkItems(string $index, array $data)
    {
        $status = true;
        $this->setIndexPaused($index);

        try {
            $jsonData = $this->getDefaultBulkJson($index);

            $indexingPage = $this->indexingPage * 2;

            foreach ($data as $item) {
                $jsonData = $this->addItemToRequest($item, $index, $jsonData);

                if (count($jsonData['body']) >= $indexingPage) {
                    $response = $this->sendBulkData($jsonData);
                    $jsonData = $this->getDefaultBulkJson($index);
                }
            }

            if (!empty($jsonData['body'])) {
                $response = $this->sendBulkData($jsonData);
            }
        } catch (\Exception $e) {
            $status = $e->getMessage();
        }

        //index processing should be enabled
        $this->setIndexProcessing($index);
        $this->refreshIndex($index);

        return $status;
    }

    /**
     * @param string $name
     * @param array $settings
     * @param array $mappings
     *
     * @return array
     */
    public function createIndex(string $name, array $settings, array $mappings = []): array
    {
        $params = [
            'index' => $name,
            'body' => [
                'settings' => $settings,
                'mappings' => [
                    'properties' => $mappings,
                ],
            ],
        ];

        // Create the index with mappings and settings now
        return $this->client->indices()->create($params);
    }

    public function getDefaultIndexSettings(): array
    {
        return [
            'number_of_shards' => $this->numberOfShards,
            'number_of_replicas' => $this->numberOfReplicas,
            'refresh_interval' => $this->indexRefreshInterval,
            'max_result_window' => $this->maxResultWindow,
            'max_rescore_window' => $this->maxRescoreWindow,
            'max_ngram_diff' => self::MAX_NGRAM_DIFF,
            'analysis' => [
                'analyzer' => [
                    'folding' => [
                        'type' => 'custom',
                        'tokenizer' => 'autocomplete',
                        'filter' => [
                            'lowercase',
                            'asciifolding',
                        ],
                    ],
                    'single_characters' => [
                        'type' => 'custom',
                        'tokenizer' => 'whitespace',
                        'filter' => [
                            'length',
                            'lowercase',
                            'asciifolding',
                            'shingle_filter',
                        ],
                    ],
                    'phone_number' => [
                        'type' => 'custom',
                        'char_filter' => [
                            'phone_char_filter',
                        ],
                        'tokenizer' => 'phone_ngram_tokenizer',
                    ],
                    'autocomplete' => [
                        'type' => 'custom',
                        'tokenizer' => 'autocomplete',
                        'filter' => [
                            'lowercase',
                        ],
                    ],
                    'autocomplete_search' => [
                        'type' => 'custom',
                        'tokenizer' => 'lowercase',
                    ],
                    'keyword_analyzer' => [
                        'filter'      => [
                            'lowercase',
                            'asciifolding',
                            'trim',
                        ],
                        'char_filter' => [],
                        'type'        => 'custom',
                        'tokenizer'   => 'keyword',
                    ],
                ],
                'char_filter' => [
                    'phone_char_filter' => [
                        'type' => 'pattern_replace',
                        'pattern' => '[\(\)\-.\s+]',
                        'replacement' => '',
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
                            'punctuation',
                            'symbol',
                        ],
                    ],
                    'phone_ngram_tokenizer' => [
                        'type' => 'ngram',
                        'min_gram' => self::BUSINESS_PHONE_MIN_WORD_LENGTH_ANALYZED,
                        'max_gram' => self::BUSINESS_PHONE_MAX_WORD_LENGTH_ANALYZED,
                        'token_chars' => [
                            'digit',
                        ],
                    ],
                ],
                'filter' => [
                    'shingle_filter' => [
                        'type' => 'shingle',
                        'min_shingle_size' => 2,
                        'max_shingle_size' => 4,
                        'output_unigrams' => true,
                        'token_separator' => '',
                    ],
                    'length' => [
                        'type' => 'length',
                        'max' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $index
     * @param int $id
     *
     * @return array
     */
    public function deleteItem(string $index, $id)
    {
        $params = [
            'index' => $index,
            'id'    => (int)$id,
        ];

        return $this->client->delete($params);
    }

    /**
     * @param string $name
     * @param array $mapping
     *
     * @return bool
     */
    public function createElasticSearchIndex(string $name, array $mapping = []): bool
    {
        $status = true;
        $settings = $this->getDefaultIndexSettings();

        try {
            $this->createIndex($name, $settings, $mapping);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());
            if (!empty($message->error->type) &&
                $message->error->type === self::INDEX_ALREADY_EXISTS_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }

    public function createAllElasticIndexes(): bool
    {
        $mappings = self::getElasticMappings();

        foreach (self::ELASTIC_INDEXES as $indexName) {
            if (!$this->createElasticSearchIndex($indexName, $mappings[$indexName])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $indexName
     *
     * @return bool
     */
    protected function deleteElasticSearchIndex(string $indexName): bool
    {
        $status = true;

        try {
            $this->client->indices()->delete(['index' => $indexName]);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->status) && $message->status == self::INDEX_NOT_FOUND_STATUS_CODE) {
                $status = true;
            }
        }

        return $status;
    }

    public function deleteAllElasticSearchIndexes(): bool
    {
        foreach (self::ELASTIC_INDEXES as $indexName) {
            if (!$this->deleteElasticSearchIndex($indexName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $index
     * @param array $data
     *
     * @return bool
     */
    public function addElasticBulkItemData(string $index, $data): bool
    {
        try {
            $status = $this->addBulkItems($index, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());
            $this->logger->error($message);
        }

        return $status;
    }

    protected function addItemToRequest(array $data, $index, $jsonData = [])
    {
        if (!$jsonData) {
            $jsonData = $this->getDefaultBulkJson($index);
        }

        $jsonData['body'][] = json_encode([
            'index' => [
                '_id'   => (int)$data['id'],
                '_index' => $index,
            ]
        ]);

        $jsonData['body'][] = json_encode($data);

        return $jsonData;
    }

    protected function sendBulkData($jsonData): array
    {
        $response = $this->client->bulk($jsonData);

        if (!empty($response['errors'])) {
            $itemsWithError = [];
            foreach ($response['items'] as $item) {
                if (array_key_exists('error', $item = array_shift($item))) {
                    $itemsWithError[] = $item;
                }
            }
            $this->logger->error('Bulk indexing error', $itemsWithError);
        }

        return $response;
    }

    protected function getDefaultBulkJson(string $index): array
    {
        return [
            'index' => $index,
            'body'  => [],
        ];
    }

    protected function setIndexPaused(string $index): void
    {
        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'refresh_interval'  => -1,
                ],
            ]
        ];

        // Create the index with mappings and settings now
        $this->client->indices()->putSettings($params);
    }

    protected function setIndexProcessing(string $index): void
    {
        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'refresh_interval'  => $this->indexRefreshInterval,
                ],
            ]
        ];

        // Create the index with mappings and settings now
        $this->client->indices()->putSettings($params);
    }

    protected function refreshIndex(string $index): void
    {
        $params = [
            'index' => $index,
        ];

        $this->client->indices()->refresh($params);
    }

    public function getIndexingPage()
    {
        return $this->indexingPage;
    }
}
