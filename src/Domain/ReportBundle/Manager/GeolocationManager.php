<?php

namespace Domain\ReportBundle\Manager;


use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;

class GeolocationManager extends DefaultManager
{
    const GEOLOCATION_COLLECTION_NAME = 'geolocation';

    /**
     * @var MongoDbManager
     */
    private $mongoDbManager;

    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @param array $value
     *
     * @return bool
     */
    public function registerGeolocationEvent($value)
    {
        $result = $this->mongoDbManager->insertOne(
            self::GEOLOCATION_COLLECTION_NAME,
            array('longitude' => $value['longitude'], 'latitude' => $value['latitude'])
        );

        return $result ? true : false;
    }

}