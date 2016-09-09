<?php

namespace Domain\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Brand;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Tag;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Translation\BrandTranslation;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Entity\Translation\TagTranslation;
use \Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;

class MigrationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('data:migration');
        $this->setDescription('Migrate all site data');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('pageStart', '1', InputOption::VALUE_OPTIONAL),
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->localePrimary = 'en';
        $this->localeSecond = 'es';

        $country = $this->em->getRepository('DomainBusinessBundle:Address\Country')->findOneBy(['shortName' => 'PR']);

        // get subscription plans

        $planMapping = [
            SubscriptionPlanInterface::CODE_FREE => 'Free',
            SubscriptionPlanInterface::CODE_PRIORITY => 'Priority',
            SubscriptionPlanInterface::CODE_PREMIUM_PLUS => 'Premium Plus',
            SubscriptionPlanInterface::CODE_PREMIUM_GOLD => 'Premium Gold',
            SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM => 'Premium Platinum',
        ];

        $plans = $this->em->getRepository('DomainBusinessBundle:SubscriptionPlan')->findAll();

        $subscriptionPlans = [];

        foreach ($plans as $item) {
            $subscriptionPlans[$planMapping[$item->getCode()]] = $item;
        }

        if ($input->getOption('pageStart')) {
            $startPage = $input->getOption('pageStart');
        } else {
            $startPage = 1;
        }

        $baseUrl = 'http://infopaginas.drxlive.com/api/businesses';

        //todo - get from params
        $withDebug = true;
        $limitPages = 2;

        for ($page = $startPage; $page <= $limitPages; $page++) {
            if ($withDebug) {
                $output->writeln('Start request page number ' . $page);
            }

            $data = $this->getCurlData($baseUrl . '?page=' . $page, $this->localePrimary);

            if ($data) {
                foreach ($data as $item) {
                    // todo - check if exists by id

                    $itemId = $item->_id;

                    if (1) {
                        if ($withDebug) {
                            $output->writeln('Starts request item with id ' . $itemId);
                        }

                        $itemPrimary = $this->getCurlData($baseUrl . '/' . $itemId, $this->localePrimary);
                        $itemSecond = $this->getCurlData($baseUrl . '/' . $itemId, $this->localeSecond);
                        $subscriptions = $this->getCurlData($baseUrl . '/' . $itemId . '/subscriptions', $this->localePrimary);

                        $this->addBusinessProfileByApiData(
                            $itemPrimary,
                            $itemSecond,
                            $subscriptions,
                            $subscriptionPlans,
                            $country,
                            $withDebug
                        );

                        if ($withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }


                    } else {
                        if ($withDebug) {
                            $output->writeln('Skip as existed item with id ' . $itemId);
                        }
                    }
                }
            }
        }

        if ($withDebug) {
            $output->writeln('Finish requests');
        }
    }

    private function getCurlData($url, $locale)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Token token=coh6fQgxVkK989OTnVoP3w",
            "Accept-Language: " . $locale
        ));

        $htmlContent = curl_exec($ch);

        if ($htmlContent) {
            $curlData = json_decode($htmlContent);

            if (!$curlData or !empty($curlData->error)) {
                //todo - set timeout and retry

                $this->output->writeln('Error occured: ' . json_encode($curlData));

                // wait 10 secs
                sleep(10);

                //todo - add counter for retry
                return $this->getCurlData($url, $locale);
            } else {
                return $curlData;
            }
        } else {
            return null;
        }
    }

    private function addBusinessProfileByApiData($itemPrimary, $itemSecond, $subscriptions, $subscriptionPlans, $country, $withDebug)
    {
        $manager = $this->getContainer()->get('domain_business.manager.business_profile');

        $business = $itemPrimary->business;

        $name = $business->name;

        $entity = $manager->createProfile();

        // populate profile

        $entity->setName($name);

        //todo - set real slug !!!
#        $entity->setSlug($data['list_slugs']);
        $entity->setSlug($name);

        $entity->setRegistrationDate(new \DateTime($business->created_at));

        $entity->setIsActive(true);

        //todo
#        $entity->setUser(?);
#        $entity->setVideo(?);
#        $entity->setLocale(?);

#        $profile = $business->profiles[0];
        $profile = $business->profile;
        $profileSecond = $itemSecond->business->profile;

        $entity->setEmail($profile->email);
        $entity->setSlogan($profile->slogan);
        $entity->setDescription($profile->description);
        $entity->setProduct($profile->products);
        $entity->setWorkingHours($profile->hours_opr);

#        $entity->setLogo(?);
#        $entity->addImage(?);
#        $entity->setPosition(?);

#        $address = $business->addresses[0];
        $address = $business->address;

        $entity->setCity($address->locality);
        $entity->setStreetAddress($address->street_address);
        $entity->setZipCode($address->postal_code);
        $entity->setExtendedAddress($address->extended_address);
        $entity->setCrossStreet($address->cross_street);
        $entity->setLatitude($address->coordinates[0]);
        $entity->setLongitude($address->coordinates[0]);

        $entity->setCountry($country);

#        $entity->setState(?);
#        $entity->setStreetNumber(?);
#        $entity->setUseMapAddress(?);

#        $entity->setGoogleAddress(?);

        $entity->setFacebookURL($profile->facebook_page_url);
        $entity->setWebsite($profile->website);

        $entity->setGoogleURL($profile->google_plus_url);
        $entity->setYoutubeURL($profile->yt_url);

#        $entity->setServiceAreasType(?);
#        $entity->setLocalities(?);
#        $entity->setSearchFts(?);
#        $entity->setActualBusinessProfile(?);
#        $entity->setUid(?);

        // todo - headings - en/es pairs

#        $entity->addCategory(?);
#        $entity->addArea(?);

        // process assigned items

        if ($business->phones) {
            foreach ($business->phones as $item) {
                $phone = new BusinessProfilePhone();
                $phone->setPhone($item->number);

                $entity->addPhone($phone);
            }
        }

        if ($profile->tags) {
            foreach ($profile->tags as $item) {
                $entity->addTag($this->loadTag($item));
            }
        }

        if ($profile->payment_methods) {
            foreach ($profile->payment_methods as $item) {
                $entity->addPaymentMethod($this->loadPaymentMethod($item));
            }
        }

        if ($profile->brands) {
            if (count($profile->brands) == count($profileSecond->brands)) {
                foreach ($profile->brands as $key => $valuePrimary) {
                    $entity->addBrand($this->loadBrand($valuePrimary, $profileSecond->brands[$key]));
                }
            } else {
                //todo - throw exception

                if ($withDebug) {
                    $this->output->writeln('Brands count are different ' . json_encode($profile->brands) . ' and ' . json_encode($profileSecond->brands));
                }
            }
        }

        if ($subscriptions) {
            foreach ($subscriptions->subscriptions as $item) {
                $key = $item->plan->contract_id;

                if (isset($subscriptionPlans[$key])) {
                    $subscription = new Subscription();

                    $subscription->setSubscriptionPlan($subscriptionPlans[$key]);
                    $subscription->setBusinessProfile($entity);
                    $subscription->setStartDate(new \DateTime($item->current_period_started_at));
                    $subscription->setEndDate(new \DateTime($item->current_period_ends_at));
                    $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);

                    $subscription = $this->saveEntity($subscription);

                    $entity->addSubscription($subscription);
                } else {
                    if ($withDebug) {
                        $this->output->writeln('Unknown subscription Plan:' . json_encode($item));
                    }
                }
            }
        }

        // process seo data

        $seoTitle = $name . ' - ' . $entity->getCity();

        if ($entity->getZipCode()) {
            $seoTitle .= ', ' . $entity->getZipCode();
        }

        $entity->setSeoTitle($seoTitle);
        $entity->setSeoDescription($name);

        $entity = $this->saveEntity($entity);

        // add translations to profile

        if ($itemSecond) {
            $translationKeys = [
                'slogan'        => 'slogan',
                'description'   => 'description',
                'products'      => 'product',
                'hours_opr'     => 'workingHours',
            ];

            foreach ($translationKeys as $key => $field) {
                if ($profile->$key != $profileSecond->$key) {
                    $translation = new BusinessProfileTranslation();
                    $this->addTranslation($translation, $profileSecond->$key, $entity, $field);
                }
            }
        }

        $this->em->flush();
    }

    private function loadTag($value)
    {
        $entity = $this->em->getRepository('DomainBusinessBundle:Tag')->findOneBy(['name' => $value]);

        if (!$entity) {
            $entity = new Tag();
            $entity->setName($value);

            $entity = $this->saveEntity($entity);

            //todo - make translations if required
        }

        return $entity;
    }

    private function loadCategory($valuePrimary, $valueSecondary)
    {
        return $this->loadEntity('Category', $valuePrimary, $valueSecondary);
    }

    private function loadBrand($valuePrimary, $valueSecondary)
    {
        return $this->loadEntity('Brand', $valuePrimary, $valueSecondary);
    }

    private function loadPaymentMethod($key)
    {
        $hardCodedList = [
            'american_express' => 'American Express',
            'ath_movil' => 'ATH Movil',
            'cash' => 'Cash',
            'check' => 'Check',
            'debit_atm' => 'Debit/ATM',
            'diners_club' => 'Diners Club',
            'discover' => 'Discover',
            'giros' => 'Giros',
            'mastercard' => 'MasterCard',
            'online_payment' => 'Online Payment',
            'paypal' => 'Paypal',
            'visa' => 'Visa',
        ];

        if (isset($hardCodedList[$key])) {
            $valuePrimary = $hardCodedList[$key];
        } else {
            $this->output->writeln('Unknown Payment Method key: ' . $key);

            $valuePrimary = $key;
        }

        return $this->loadEntity('PaymentMethod', $valuePrimary, $valuePrimary);
    }

    private function loadEntity($className, $valuePrimary, $valueSecondary)
    {
        $entity = $this->em->getRepository('DomainBusinessBundle:' . $className)->findOneBy(['name' => $valuePrimary]);

        if (!$entity) {
            $classNameEntity = '\Domain\BusinessBundle\Entity\\' . $className;

            $entity = new $classNameEntity();
            $entity->setName($valuePrimary);

            $entity = $this->saveEntity($entity);
            $this->em->persist($entity);

            $translationClassName = 'Domain\BusinessBundle\Entity\Translation\\' . $className . 'Translation';

            $translation = new $translationClassName();

            $this->addTranslation($translation, $valueSecondary, $entity);
        }

        return $entity;
    }

    private function addTranslation($translation, $value, $object, $fieldName = 'name', $locale = null)
    {
        if (!$locale) {
            $locale = $this->localeSecond;
        }

        $translation->setField($fieldName);
        $translation->setContent($value);
        $translation->setLocale($locale);
        $translation->setObject($object);

        $this->em->persist($translation);
    }

    private function saveEntity($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
