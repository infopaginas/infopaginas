<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\AbstractQuery;
use Domain\BannerBundle\Entity\Banner;
use Domain\BannerBundle\Model\TypeModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190916151948 extends AbstractMigration  implements ContainerAwareInterface
{
    private $em;
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
        $qb = $this->em->createQueryBuilder()
            ->select('b.htmlId')
            ->from('DomainBannerBundle:Banner', 'b')
        ;
        $htmlIds = $qb->getQuery()->getResult();

        foreach ($htmlIds as $i => $htmlId) {
            $htmlIds[$i] = $htmlIds[$i]['htmlId'];
        }

        $banners = TypeModel::getDefaultBannerSettings();
        foreach ($banners as $item) {
            if (!in_array($item['htmlId'], $htmlIds)) {
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
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
