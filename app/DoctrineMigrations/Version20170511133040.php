<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\LandingPageShortCut;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170511133040 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->updateLandingPageShortCutSearchOrder();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }

    protected function updateLandingPageShortCutSearchOrder()
    {
        $defaultPosition = 1;

        $shortCuts = $this->em->getRepository(LandingPageShortCut::class)->findAll();

        foreach ($shortCuts as $shortCut) {
            $searchItems = $shortCut->getSearchItems();
            $currentPosition = $defaultPosition;

            foreach ($searchItems as $searchItem) {
                $this->addSql(
                    "UPDATE landing_page_short_cut_search SET position = '" . $currentPosition . "'
                    WHERE id = " . $searchItem->getId()
                );

                $currentPosition++;
            }
        }
    }
}
