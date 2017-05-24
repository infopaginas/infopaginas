<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170104101047 extends AbstractMigration implements ContainerAwareInterface
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
        $updatePaymentMethod = [
            'Cash' => [
                'nameEn' => 'Cash',
                'nameEs' => 'Efectivo',
                'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_CASH,
            ],
            'Check' => [
                'nameEn' => 'Check',
                'nameEs' => 'Cheque',
                'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_CHECK,
            ],
            'Paypal' => [
                'nameEn' => 'PayPal',
                'nameEs' => 'PayPal',
                'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_PAYPAL,
            ],
            'ATH Movil' => [
                'nameEn' => 'ATHMovil',
                'nameEs' => 'ATHMovil',
                'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_ATH_MOVIL,
            ],
            'Online Payment' => [
                'nameEn' => 'Online Payment',
                'nameEs' => 'Online Payment',
                'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_ONLINE,
            ],
        ];

        foreach ($updatePaymentMethod as $key => $item) {
            /* @var $paymentMethod PaymentMethod */
            $paymentMethod = $this->getPaymentMethodByName($key);

            if ($paymentMethod) {
                $paymentMethod->setName($item['nameEn']);
                $paymentMethod->setType($item['type']);

                $paymentMethod = $this->addTranslation($paymentMethod, $item, 'en');
                $paymentMethod = $this->addTranslation($paymentMethod, $item, 'es');
            } else {
                $paymentMethod = $this->em->getRepository('DomainBusinessBundle:PaymentMethod')
                    ->findOneBy(['type' => $item['type']]);

                if (!$paymentMethod) {
                    $paymentMethod = $this->addPaymentMethod($item);
                }
            }
        }

        $this->em->flush();

        $debit = [
            'aliases' => [
                'Debit/ATM',
                'MasterCard',
                'Visa',
                'American Express',
                'Discover',
                'Diners Club',
            ],
            'nameEn' => 'Debit Card',
            'nameEs' => 'Debito',
            'type'   => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
        ];

        $data = [];

        $debitPaymentMethod = $this->em->getRepository('DomainBusinessBundle:PaymentMethod')
            ->findOneBy(['type' => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT]);

        if (!$debitPaymentMethod) {
            $debitPaymentMethod = $this->addPaymentMethod($debit);
        }

        $this->em->persist($debitPaymentMethod);
        $this->em->flush();

        foreach ($debit['aliases'] as $item) {
            $paymentMethod = $this->getPaymentMethodByName($item);
            $data[] = $paymentMethod;
        }

        $businesses = $this->getBusinessIteratorByPaymentMethodIds($data);

        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var $business BusinessProfile */
            $business = $row[0];

            foreach ($data as $item) {
                if ($business->getPaymentMethods()->contains($item)) {
                    $business->removePaymentMethod($item);
                }
            }

            $business->addPaymentMethod($debitPaymentMethod);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();

                $debitPaymentMethod = $this->em->getRepository('DomainBusinessBundle:PaymentMethod')
                    ->findOneBy(['type' => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT]);

                foreach ($debit['aliases'] as $item) {
                    $paymentMethod = $this->getPaymentMethodByName($item);
                    $data[] = $paymentMethod;
                }
            }
            $i ++;

            $this->em->detach($row[0]);
        }

        $this->em->flush();

        foreach ($debit['aliases'] as $item) {
            $paymentMethod = $this->getPaymentMethodByName($item);

            if ($paymentMethod) {
                $this->em->remove($paymentMethod);
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

    protected function getBusinessIteratorByPaymentMethodIds($ids)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('b.id')
            ->distinct()
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->join('b.paymentMethods', 'pm')
            ->where('pm.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        $businessesIds = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($businessesIds as $row) {
            $data[] = $row['id'];
        }

        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->where('b.id IN (:ids)')
            ->setParameter('ids', $data)
        ;

        $query = $this->em->createQuery($qb->getDQL());
        $query->setParameter('ids', $data);

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    protected function getPaymentMethodByName($name)
    {
        $query = $this->em->createQueryBuilder()
            ->select('pm')
            ->from('DomainBusinessBundle:PaymentMethod', 'pm')
            ->where('pm.name = :name')
            ->setParameter('name', $name)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    protected function addTranslation(PaymentMethod $paymentMethod, $item, $locale)
    {
        foreach (PaymentMethod::getTranslatableFields() as $field) {
            $translation = $paymentMethod->getTranslationItem($field, $locale);

            if ($translation) {
                $translation->setContent($item[$field . ucfirst($locale)]);
            } else {
                $translation = new PaymentMethodTranslation();

                $translation->setField($field);
                $translation->setLocale($locale);
                $translation->setContent($item[$field . ucfirst($locale)]);
                $translation->setObject($paymentMethod);
                $this->em->persist($translation);
            }
        }

        return $paymentMethod;
    }

    protected function addPaymentMethod($data)
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName($data['nameEn']);
        $paymentMethod->setType($data['type']);
        $paymentMethod = $this->addTranslation($paymentMethod, $data, 'es');
        $paymentMethod = $this->addTranslation($paymentMethod, $data, 'en');

        $this->em->persist($paymentMethod);

        return $paymentMethod;
    }
}
