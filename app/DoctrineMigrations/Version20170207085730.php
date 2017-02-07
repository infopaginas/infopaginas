<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207085730 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewSubscriptionPlan()) {
            $code = SubscriptionPlanInterface::CODE_SUPER_VM;
            $data = SubscriptionPlan::getCodeNames();
            $value = $data[$code];

            $object = new SubscriptionPlan();
            $object->setName($value);
            $object->setCode($code);
            $object->setRank($code);

            $translation = new SubscriptionPlanTranslation();
            $translation->setContent(sprintf('Spain %s', $value));
            $translation->setField('name');
            $translation->setLocale('es');
            $translation->setObject($object);

            $this->em->persist($object);
            $this->em->persist($translation);

            $this->em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function checkNewSubscriptionPlan()
    {
        $subscriptionPlan = $this->em->getRepository('DomainBusinessBundle:SubscriptionPlan')->findOneBy(
            [
                'code' => SubscriptionPlanInterface::CODE_SUPER_VM
            ]
        );

        return (bool)$subscriptionPlan;
    }
}
