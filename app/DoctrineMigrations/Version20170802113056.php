<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170802113056 extends AbstractMigration implements ContainerAwareInterface
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
        $paymentMethodData = PaymentMethod::getPaymentMethodData()[PaymentMethod::PAYMENT_METHOD_TYPE_ATH];

        $name = $paymentMethodData['nameEn'];
        $type = $paymentMethodData['type'];

        $paymentMethod = $this->getPaymentMethodByType($type);

        if (!$paymentMethod) {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setName($name);
            $paymentMethod->setType($type);

            $this->em->persist($paymentMethod);
            $this->em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    /**
     * @param string $type
     *
     * @return PaymentMethod|null
     */
    protected function getPaymentMethodByType($type)
    {
        $paymentMethod = $this->em->getRepository(PaymentMethod::class)->findOneBy([
            'type' => $type,
        ]);

        return $paymentMethod;
    }
}
