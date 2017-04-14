<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BannerBundle\DataFixtures\ORM\LoadTypeData;
use Domain\BannerBundle\Entity\Type as BannerType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Version20170413133836 extends AbstractMigration implements ContainerAwareInterface
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
    public function up(Schema $schema)
    {
        $bannersData = LoadTypeData::getData();

        foreach ($bannersData as $item) {
            $bannerType = $this->getBannerTypeByItem($item);

            $bannerType->setName($item['name']);
            $bannerType->setPlacement($item['placement']);
            $bannerType->setComment($item['comment']);
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    /**
     * @param array $item
     *
     * @return BannerType
     */
    protected function getBannerTypeByItem($item)
    {
        $bannerType = $this->em->getRepository('DomainBannerBundle:Type')->findOneBy(
            [
                'code' => $item['code'],
            ]
        );

        if (!$bannerType) {
            $bannerType = new BannerType();
            $bannerType->setCode($item['code']);

            $this->em->persist($bannerType);
        }

        return $bannerType;
    }
}
