<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Model\PageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170504133022 extends AbstractMigration implements ContainerAwareInterface
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
        $data = [
            'title'       => 'Landing Page',
            'code'        => PageInterface::CODE_LANDING,
            'description' => 'Landing Page Description',
            'body'        => 'Landing Page Body',
            'isPublished' => true,
            'slug'        => '',
        ];

        $object = new Page();
        $object->setTitle($data['title']);
        $object->setCode($data['code']);
        $object->setDescription($data['description']);
        $object->setBody($data['body']);
        $object->setIsPublished($data['isPublished']);
        $object->setSlug($data['slug']);

        $object = $this->container->get('domain_page.manager.page')->setPageSeoData($object, $this->container);

        $this->em->persist($object);
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
