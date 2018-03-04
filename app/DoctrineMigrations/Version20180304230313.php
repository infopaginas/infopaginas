<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20180304230313 extends AbstractMigration implements ContainerAwareInterface
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
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function up(Schema $schema)
    {
       $this->updateHomeTitle();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function updateHomeTitle()
    {
        /** @var Page $page */
        $page = $this->em->getRepository(Page::class)->findOneBy([
            'code' => PageInterface::CODE_LANDING,
        ]);

        if ($page) {
            /** @var PageTranslation $pageTranslation */
            $pageTranslation = $this->em->getRepository(PageTranslation::class)->findOneBy([
                'object' => $page->getId(),
                'field' => 'title',
            ]);

            if ($pageTranslation) {
                $pageTranslation->setContent('¡Encuentra lo que buscas al instante!');

                $this->em->persist($pageTranslation);
                $this->em->flush();
            }
        }
    }
}
