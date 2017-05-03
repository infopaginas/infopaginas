<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationPaymentFixCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var array $mergePaymentMethods
     */
    protected $mergePaymentMethods = [];

    /**
     * @var SubscriptionStatusManager $subscriptionManager
     */
    protected $subscriptionManager;

    protected function configure()
    {
        $this->setName('data:migration:payment-fix');
        $this->setDescription('Update payment methods');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $updated = 0;
        $added   = $this->addDefaultPaymentMethods();

        $paymentMethods = $this->em->getRepository(PaymentMethod::class)->findAll();

        $this->mergePaymentMethods = self::getPaymentMethodsForMerge();

        foreach ($paymentMethods as $paymentMethod) {
            $updated += $this->updatePaymentMethod($paymentMethod);
        }

        $output->writeln('Done');
        $output->writeln($added . ' Payment methods were added');
        $output->writeln($updated . ' Businesses were updated');
    }

    /**
     * @param PaymentMethod $item
     *
     * @return int
     */
    private function updatePaymentMethod($item)
    {
        $updated = 0;

        if (!empty($this->mergePaymentMethods[$item->getName()]) and !$item->getType()) {
            $parent = $this->getPaymentMethodByType($this->mergePaymentMethods[$item->getName()]);

            foreach ($item->getBusinessProfiles() as $business) {
                /* @var BusinessProfile $business */
                $business->removePaymentMethod($item);

                if ($parent and !$business->getPaymentMethods()->contains($parent)) {
                    $business->addPaymentMethod($parent);
                }

                $updated++;
            }

            $this->em->remove($item);
            $this->em->flush();
        }

        return $updated;
    }

    public static function getPaymentMethodsForMerge()
    {
        return [
            'Debit/ATM'         => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'MasterCard'        => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'Visa'              => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'American Express'  => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'Discover'          => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'Diners Club'       => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'Paypal'            => PaymentMethod::PAYMENT_METHOD_TYPE_PAYPAL,
            'ATH Movil'         => PaymentMethod::PAYMENT_METHOD_TYPE_ATH_MOVIL,
        ];
    }

    /**
     * @return int
     */
    protected function addDefaultPaymentMethods()
    {
        $added = 0;

        $data = PaymentMethod::getPaymentMethodData();

        foreach ($data as $item) {
            $paymentMethod = $this->getPaymentMethodByType($item['type']);

            if (!$paymentMethod) {
                $object = new PaymentMethod();
                $object->setName($item['nameEn']);
                $object->setType($item['type']);

                $translation = new PaymentMethodTranslation();
                $translation->setContent($item['nameEs']);
                $translation->setField('name');
                $translation->setLocale('es');
                $translation->setObject($object);

                $this->em->persist($object);
                $this->em->persist($translation);

                $added++;
            }
        }

        $this->em->flush();

        return $added;
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
