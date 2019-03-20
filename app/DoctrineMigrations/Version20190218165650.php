<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20190218165650 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        $googleApiKey = 'GOOGLE_API_KEY';
        $mapboxDescription = 'Used for access to MapBox';
        $mapboxTitle = 'MapBox api key';
        $keyValue = $this->container->getParameter('mapbox_api_key');
        $latitudeValue = '18.2188';
        $longitudeValue = '-66.4371';

        $this->addSql('
            UPDATE config
            SET value = \'' . $keyValue . '\', key = \'' . ConfigInterface::MAPBOX_API_KEY . '\',
            title = \'' . $mapboxTitle . '\', description = \'' . $mapboxDescription . '\'
            WHERE key = \'' . $googleApiKey . '\''
        );
        $this->addSql('UPDATE config SET value = \'' . $latitudeValue . '\'
            WHERE key = \'' . ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE . '\'');
        $this->addSql('UPDATE config SET value = \'' . $longitudeValue . '\'
            WHERE key = \'' . ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE . '\'');
    }

    public function down(Schema $schema)
    {

    }
}
