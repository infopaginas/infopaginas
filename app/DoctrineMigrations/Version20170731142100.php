<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BannerBundle\Entity\Banner;
use Domain\BannerBundle\Model\TypeModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170731142100 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $banners = TypeModel::getDefaultBannerSettings();

        foreach ($banners as $item) {
            $banner = new Banner();

            $banner->setTitle($item['name']);
            $banner->setDescription($item['comment']);
            $banner->setPlacement($item['placement']);
            $banner->setComment($item['comment']);
            $banner->setCode($item['code']);
            $banner->setHtmlId($item['htmlId']);
            $banner->setSlotId($item['slotId']);

            $this->em->persist($banner);
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }
}
