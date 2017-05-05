<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Util\SlugUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;

class MigrationPaymentMethodFixCommand extends ContainerAwareCommand
{
    const DEFAULT_LOCALE  = 'en';

    const API_BASE_URL    = 'http://infopaginas.drxlive.com/api/businesses';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var bool $withDebug
     */
    protected $withDebug;

    protected function configure()
    {
        $this->setName('data:migration:payment-method-fix');
        $this->setDescription('Update Locality');
        $this->setDefinition(
            new InputDefinition([
                new InputOption('withDebug', 'd'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        if ($input->getOption('pageStart')) {
            $pageStart = $input->getOption('pageStart');
        } else {
            $pageStart = 1;
        }

        if ($input->getOption('pageCountLimit')) {
            $pageCountLimit = $input->getOption('pageCountLimit');
        } else {
            $pageCountLimit = 1;
        }

        if ($input->getOption('withDebug')) {
            $this->withDebug = true;
        } else {
            $this->withDebug = false;
        }

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                $output->writeln('Start request page number ' . $page);
            }

            $data = $this->getCurlData($this->getBusinessesByPageUrl($page), self::DEFAULT_LOCALE);

            if ($data) {
                foreach ($data as $item) {
                    $itemId = $item->_id;

                    /* @var BusinessProfile $businessProfile */
                    $businessProfile = $this->em->getRepository(BusinessProfile::class)->findOneBy(
                        [
                            'uid' => $itemId,
                        ]
                    );

                    if ($businessProfile) {
                        $data = $this->getCurlData($this->getBusinessByUid($itemId), self::DEFAULT_LOCALE);

                        $businessProfile = $this->removeOldBusinessPaymentMethods($businessProfile);

                        $this->updateBusinessPaymentMethods($data, $businessProfile);

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip item with id ' . $itemId);
                        }
                    }

                    $this->em->flush();
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        if ($this->withDebug) {
            $output->writeln('Finish requests');
        }
    }

    /**
     * @param string $url
     * @param string $locale
     *
     * @return mixed
     */
    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale,
        ]);

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    /**
     * @param \stdClass         $data
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function updateBusinessPaymentMethods($data, $businessProfile)
    {
        $paymentMethods = $data->business->profile->payment_methods;

        if ($paymentMethods) {
            foreach ($paymentMethods as $item) {
                $paymentMethod = $this->getPaymentMethod($item);

                if ($paymentMethod and !$businessProfile->getPaymentMethods()->contains($paymentMethod)) {
                    $businessProfile->addPaymentMethod($paymentMethod);
                }
            }
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile   $businessProfile
     *
     * @return BusinessProfile   $businessProfile
     */
    private function removeOldBusinessPaymentMethods($businessProfile)
    {
        $paymentMethods = $businessProfile->getPaymentMethods();

        foreach ($paymentMethods as $paymentMethod) {
            $businessProfile->removePaymentMethod($paymentMethod);
        }

        return $businessProfile;
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    private function getBusinessesByPageUrl($pageNumber)
    {
        return self::API_BASE_URL . '?page=' . $pageNumber;
    }

    /**
     * @param string $uid
     *
     * @return string
     */
    private function getBusinessByUid($uid)
    {
        return self::API_BASE_URL . '/' . $uid;
    }

    /**
     * @param string $key
     *
     * @return PaymentMethod|null
     */
    private function getPaymentMethod($key)
    {
        $hardCodedList = [
            'american_express'  => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'debit_atm'         => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'diners_club'       => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'discover'          => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'mastercard'        => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'visa'              => PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT,
            'ath_movil'         => PaymentMethod::PAYMENT_METHOD_TYPE_ATH_MOVIL,
            'cash'              => PaymentMethod::PAYMENT_METHOD_TYPE_CASH,
            'check'             => PaymentMethod::PAYMENT_METHOD_TYPE_CHECK,
            'online_payment'    => PaymentMethod::PAYMENT_METHOD_TYPE_ONLINE,
            'paypal'            => PaymentMethod::PAYMENT_METHOD_TYPE_PAYPAL,
        ];

        if (isset($hardCodedList[$key])) {
            $valuePrimary = $hardCodedList[$key];

            $paymentMethod = $this->em->getRepository(PaymentMethod::class)->findOneBy([
                'type' => $valuePrimary,
            ]);
        } else {
            $this->output->writeln('Unknown Payment Method key: ' . $key);
            $paymentMethod = null;
        }

        return $paymentMethod;
    }
}
