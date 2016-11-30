<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Util\SlugUtil;
use Domain\MenuBundle\Model\MenuModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Tag;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Entity\Translation\TagTranslation;
use \Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;

class MigrationCommand extends ContainerAwareCommand
{
    const CATEGORY_SEPARATOR = ' / ';

    protected function configure()
    {
        $this->setName('data:migration');
        $this->setDescription('Migrate all site data');
        $this->setDefinition(
            new InputDefinition(array(
                new InputOption('withDebug', 'd'),
                new InputOption('skipImages', 'i'),
                new InputOption('pageCountLimit', 'pl', InputOption::VALUE_OPTIONAL),
                new InputOption('pageStart', 'ps', InputOption::VALUE_OPTIONAL),
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

        $this->subscriptionPlans = [];

        foreach ($plans as $item) {
            $this->subscriptionPlans[$planMapping[$item->getCode()]] = $item;
        }

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

        if ($input->getOption('skipImages')) {
            $this->skipImages = true;
        } else {
            $this->skipImages = false;
        }

        $baseUrl = 'http://infopaginas.drxlive.com/api/businesses';

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {
            if ($this->withDebug) {
                $output->writeln('Start request page number ' . $page);
            }

            $data = $this->getCurlData($baseUrl . '?page=' . $page, $this->localePrimary);

            if ($data) {
                foreach ($data as $item) {
                    // todo - check if exists by id

                    $itemId = $item->_id;

                    if (1) {
                        if ($this->withDebug) {
                            $output->writeln('Starts request item with id ' . $itemId);
                        }

                        $itemPrimary = $this->getCurlData($baseUrl . '/' . $itemId, $this->localePrimary);
                        $itemSecond = $this->getCurlData($baseUrl . '/' . $itemId, $this->localeSecond);
                        $subscriptions = $this->getCurlData($baseUrl . '/' . $itemId . '/subscriptions', $this->localePrimary);

                        $localities = [];

                        if (!empty($item->service_areas)) {
                            foreach ($item->service_areas as $locality) {
                                // in API data not unique
                                $localities[$locality->locality] = $locality;
                            }
                        }

                        $radius = $item->radius_served;

                        $this->addBusinessProfileByApiData(
                            $itemPrimary,
                            $itemSecond,
                            $subscriptions,
                            $localities,
                            $radius,
                            $country
                        );

                        if ($this->withDebug) {
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip as existed item with id ' . $itemId);
                        }
                    }
                }
            }
        }

        if ($this->withDebug) {
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

    private function addBusinessProfileByApiData($itemPrimary, $itemSecond, $subscriptions, $localities, $radius, $country)
    {
        $manager = $this->getContainer()->get('domain_business.manager.business_profile');

        $business = $itemPrimary->business;

        $name = $business->name;

        $entity = $manager->createProfile();

        // populate profile

        $entity->setUid($business->id);
        $entity->setName($name);

        //todo - set real slug !!!
        $entity->setSlug($business->slug);

        $entity->setSlugEn($business->slug);
        $entity->setSlugEs($itemSecond->business->slug);

        $entity->setRegistrationDate(new \DateTime($business->created_at));

        $entity->setIsActive(true);

        //todo
#        $entity->setUser(?);
#        $entity->setVideo(?);
#        $entity->setLocale(?);

        $profile = $business->profile;
        $profileSecond = $itemSecond->business->profile;

        $entity->setEmail($profile->email);
        $entity->setSlogan($profile->slogan);
        $entity->setDescription($profile->description);
        $entity->setProduct($profile->products);
        $entity->setWorkingHours($profile->hours_opr);

#        $entity->setPosition(?);

        $address = $business->address;

        $entity->setCity($address->locality);
        $entity->setStreetAddress($address->street_address);
        $entity->setZipCode($address->postal_code);
        $entity->setExtendedAddress($address->extended_address);
        $entity->setCrossStreet($address->cross_street);
        $entity->setLongitude($address->coordinates[0]);
        $entity->setLatitude($address->coordinates[1]);

        $entity->setCountry($country);

#        $entity->setState(?);
#        $entity->setStreetNumber(?);
#        $entity->setUseMapAddress(?);

#        $entity->setGoogleAddress(?);

        $entity->setFacebookURL($profile->facebook_page_url);
        $entity->setWebsite($profile->website);

        $entity->setGoogleURL($profile->google_plus_url);
        $entity->setYoutubeURL($profile->yt_url);

#        $entity->setActualBusinessProfile(?);

        // process assigned items

        if (!$this->skipImages and $profile->images) {
            $managerGallery = $this->getContainer()->get('domain_business.manager.business_gallery');

            foreach ($profile->images as $image) {
                $path = 'http://assets3.drxlive.com' . $image->image->url;

                if ($image->label == 'logo') {
                    $isLogo = true;
                } else {
                    $isLogo = false;
                }

                $managerGallery->createNewEntryFromRemoteFile($entity, $path, $isLogo);
            }
        }

        if ($localities) {
            $entity->setServiceAreasType('locality');

            foreach ($localities as $item) {
                $locality = $this->loadLocality($item);

                $entity->addLocality($locality);

                if ($locality->getNeighborhoods()) {
                    foreach ($locality->getNeighborhoods() as $neighborhood) {
                        $entity->addNeighborhood($neighborhood);
                    }
                }
            }
        } else {
            $entity->setMilesOfMyBusiness($radius);
            $entity->setServiceAreasType('area');
        }

        if (!$entity->getCatalogLocality()) {
            $catalogLocality = $this->loadLocality($address);
            $entity->setCatalogLocality($catalogLocality);
        }

        if ($profile->headings) {
            // categories

            $pairs = [];
            $pair = [];

            foreach ($profile->headings as $key => $value) {
                // process - en/es pair
                $pair[] = $value;

                if ($key %2 != 0) {
                    $pairs[] = $pair;

                    $pair = [];
                }
            }

            foreach ($pairs as $pair) {
                $category = $this->loadCategory($pair);

                if ($category) {
                    $entity = $this->addBusinessProfileCategory($entity, $category);
                }
            }
        }

        if ($business->phones) {
            $phones = [];

            foreach ($business->phones as $item) {
                // in API data not unique
                $phones[$item->number] = $item->number;
            }

            foreach ($phones as $item) {
                $phone = new BusinessProfilePhone();
                $phone->setPhone($item);

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

        $brandPrimary   = '';
        $brandSecond    = '';

        if ($profile->brands or $profileSecond->brands) {
            if ($profile->brands) {
                $brandPrimary = implode(PHP_EOL, $profile->brands);
            }

            if ($profileSecond->brands) {
                $brandSecond = implode(PHP_EOL, $profileSecond->brands);
            }

            $entity->setBrands($brandPrimary);
        }

        if ($subscriptions) {
            foreach ($subscriptions->subscriptions as $item) {
                $key = $item->plan->contract_id;

                if (isset($this->subscriptionPlans[$key])) {
                    $subscription = new Subscription();

                    $subscription->setSubscriptionPlan($this->subscriptionPlans[$key]);
                    $subscription->setBusinessProfile($entity);
                    $subscription->setStartDate(new \DateTime($item->current_period_started_at));

                    $endDate = new \DateTime($item->current_period_ends_at);
                    $now = new \DateTime('now');

                    $subscription->setEndDate($endDate);

                    if ($endDate >= $now) {
                        $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
                    }

                    $subscription = $this->saveEntity($subscription);

                    $entity->addSubscription($subscription);
                } else {
                    if ($this->withDebug) {
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

        // set translation vectors

        if ($this->localePrimary == 'en') {
            $nameEn = $itemPrimary->business->name;
            $nameEs = $itemSecond->business->name;

            $descriptionEn = $profile->description;
            $descriptionEs = $profileSecond->description;
        } else {
            $nameEs = $itemPrimary->business->name;
            $nameEn = $itemSecond->business->name;

            $descriptionEs = $profile->description;
            $descriptionEn = $profileSecond->description;
        }

        $entity->setNameEn($nameEn);
        $entity->setNameEs($nameEs);

        $entity->setDescriptionEn($descriptionEn);
        $entity->setDescriptionEs($descriptionEs);

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

            // special case for brands

            if (($brandPrimary or $brandSecond) and ($brandPrimary != $brandSecond)) {
                $translation = new BusinessProfileTranslation();
                $this->addTranslation($translation, $brandSecond, $entity, 'brands');
            }
        }

        $this->em->flush();
    }

    private function loadCategory($pair)
    {
        // first seems to be english

        if ($this->localePrimary == 'en') {
            $valuePrimary = $pair[0];
            $valueSecondary = $pair[1];

            $valueEn = $valuePrimary;
            $valueEs = $valueSecondary;
        } else {
            $valuePrimary = $pair[1];
            $valueSecondary = $pair[0];

            $valueEs = $valuePrimary;
            $valueEn = $valueSecondary;
        }

        //search category as is
        $entity = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(['name' => $valuePrimary]);

        if (!$entity) {
            //search category as subcategory
            $parentValue    = $this->parseCategoryName($valuePrimary);

            $entity = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(['name' => $valuePrimary]);

            if (!$entity) {
                //get parent category
                $parentEntity = $this->getParentCategory($parentValue);

                if ($parentEntity) {
                    $subcategoryNameEn = $this->convertSubcategoryName($valueEn, $parentEntity->getSearchTextEn());
                    $subcategoryNameEs = $this->convertSubcategoryName($valueEn, $parentEntity->getSearchTextEs());

                    $entity = new Category();
                    $entity->setName($valuePrimary);

                    $entity->setSlugEn(SlugUtil::convertSlug($valueEn));
                    $entity->setSlugEs(SlugUtil::convertSlug($valueEs));

                    $entity->setSearchTextEn($subcategoryNameEn);
                    $entity->setSearchTextEs($subcategoryNameEs);
                    $entity->setParent($parentEntity);

                    $entity = $this->saveEntity($entity);

                    $className = 'Category';

                    $translationClassName = 'Domain\BusinessBundle\Entity\Translation\\' . $className . 'Translation';

                    $translation = new $translationClassName();

                    $this->addTranslation($translation, $valueSecondary, $entity);
                }
            }
        }

        return $entity;
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

    private function loadLocality($item)
    {
        $className  = 'Locality';
        $repository = $this->em->getRepository('DomainBusinessBundle:' . $className);

        $entity = $repository->getLocalityByName(trim($item->locality));

        if (!$entity) {
            $classNameEntity = '\Domain\BusinessBundle\Entity\\' . $className;

            $entity = new $classNameEntity();
            $entity->setName($item->locality);

            $entity = $this->saveEntity($entity);
            // todo - add area?
        }

        if (!$entity->getLongitude()) {
            $entity->setLongitude($item->coordinates[0]);
            $entity->setLatitude($item->coordinates[1]);
        }

        return $entity;
    }

    private function loadArea($valuePrimary)
    {
        return $this->loadEntity('Area', $valuePrimary, $valuePrimary);
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

    private function parseCategoryName($name)
    {
        //todo

        $categories = MenuModel::getAllCategoriesNames();
        $categories[] = [
            'en' => 'Auto',
            'es' => 'Automobiles',
        ];

        $categories[] = [
            'en' => 'Photograph',
            'es' => 'Fotógrafos',
        ];

        $categories[] = [
            'en' => 'Photograph',
            'es' => 'Photographic',
        ];

        $categories[] = [
            'en' => 'Clothing',
            'es' => 'Ropa',
        ];

        $separators = $this->getCategorySeparators();

        foreach ($categories as $item) {
            foreach ($separators as $separator) {
                if (strpos(strtolower($name), strtolower($item['en'] . $separator)) === 0 or
                    strpos(strtolower($name), strtolower($item['es'] . $separator)) === 0) {
                    return $item['en'];
                }
            }
        }

        return $name;
    }

    private function convertSubcategoryName($name, $parentName)
    {
        $convertedName = $name;
        $separators    = $this->getCategorySeparators();

        foreach ($separators as $separator) {
            $convertedName = str_replace($parentName . $separator, '', $convertedName);
        }

        return $parentName . self::CATEGORY_SEPARATOR . $name;
    }

    private function getCategorySeparators()
    {
        return [' - ', ' / ', '/'];
    }

    private function getParentCategory($parentName)
    {
        $entity = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(['name' => $parentName]);

        if (!$entity) {
            $entity = $this->em->getRepository('DomainBusinessBundle:Category')->findOneBy(
                [
                    'slug' => strtolower(MenuModel::getOtherCategoriesNames()[MenuModel::CODE_UNDEFINED]['en'])
                ]
            );
        }

        return $entity;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param Category $category
     *
     * @return BusinessProfile
     */
    private function addBusinessProfileCategory(BusinessProfile $businessProfile, Category $category)
    {
        $businessProfileCategory = $businessProfile->getCategory();
        $newParentCategory = $category->getParent();

        if ($businessProfileCategory) {
            if ($businessProfileCategory->getChildren()->contains($category) and
                !$businessProfile->getCategories()->contains($category)
            ) {
                $businessProfile->addCategory($category);
            }
        } else {
            if ($newParentCategory) {
                $businessProfile->addCategory($newParentCategory);
            }

            $businessProfile->addCategory($category);
        }

        return $businessProfile;
    }
}
