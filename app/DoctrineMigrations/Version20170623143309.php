<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170623143309 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $container ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $businessProfileManager = $this->container->get('domain_business.manager.business_profile');

        $businessProfileManager->handleElasticSearchIndexRefresh();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
