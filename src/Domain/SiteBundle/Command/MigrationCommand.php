<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Model\CategoryModel;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
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
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class MigrationCommand extends ContainerAwareCommand
{
    const SYSTEM_CATEGORY_SEPARATOR = ' / ';
    const CATEGORY_NAME_MAX_LENGTH = 250;
    const TAG_SEPARATOR = ';';
    const TWITTER_URL_PREFIX = 'https://twitter.com/';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var array $categoryEnMergeMapping
     */
    protected $categoryEnMergeMapping;

    /**
     * @var array $categoryEsMergeMapping
     */
    protected $categoryEsMergeMapping;

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
                new InputOption('twitterOnly', 't'),
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->output = $output;

        $this->localePrimary = 'en';
        $this->localeSecond = 'es';

        $this->totalTimer = 0;

        $this->categoryEnMergeMapping = CategoryModel::getCategoryEnMergeMapping();
        $this->categoryEsMergeMapping = CategoryModel::getCategoryEsMergeMapping();

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

        if ($input->getOption('twitterOnly')) {
            $this->twitterOnly = true;
        } else {
            $this->twitterOnly = false;
        }

        $baseUrl = 'http://infopaginas.drxlive.com/api/businesses';

        if ($this->withDebug) {
            $pageCurlTime = 0;
            $pageDbTime = 0;
            $itemCounter = 1;
            $this->totalTimer = microtime(true);
        }

        for ($page = $pageStart; $page <= ($pageStart + $pageCountLimit); $page++) {

            // see http://www.doctrine-project.org/2009/08/07/doctrine2-batch-processing.html
            $country = $this->em->getRepository('DomainBusinessBundle:Address\Country')
                ->findOneBy(['shortName' => 'PR']);

            // get subscription plans

            $planMapping = [
                SubscriptionPlanInterface::CODE_FREE => 'Free',
                SubscriptionPlanInterface::CODE_PRIORITY => 'Priority',
                SubscriptionPlanInterface::CODE_PREMIUM_PLUS => 'Premium Plus',
                SubscriptionPlanInterface::CODE_PREMIUM_GOLD => 'Premium Gold',
                SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM => 'Premium Platinum',
                SubscriptionPlanInterface::CODE_SUPER_VM => 'SuperVM',
            ];

            $plans = $this->em->getRepository('DomainBusinessBundle:SubscriptionPlan')->findAll();

            $this->subscriptionPlans = [];

            foreach ($plans as $item) {
                $this->subscriptionPlans[$planMapping[$item->getCode()]] = $item;
            }

            if ($this->withDebug) {
                $output->writeln(
                    'Start request page number ' . $page .
                    '; Curl Timer: ' . ($pageCurlTime/$itemCounter) .
                    '; DB Timer: ' . ($pageDbTime/$itemCounter)
                );
            }

            $data = $this->getCurlData($baseUrl . '?page=' . $page, $this->localePrimary);

            if ($data) {
                foreach ($data as $item) {
                    // todo - check if exists by id

                    $itemId = $item->_id;

                    $businessProfile = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
                        ->findOneBy(['uid' => $itemId]);

                    if (!$businessProfile or ($businessProfile and $this->twitterOnly)) {
                        if ($this->withDebug) {
                            $itemCounter ++;
                            $output->writeln('Starts request item with id ' . $itemId);
                            $curlTimer = microtime(true);
                        }

                        $itemPrimary = $this->getCurlData($baseUrl . '/' . $itemId, $this->localePrimary);
                        $itemSecond = $this->getCurlData($baseUrl . '/' . $itemId, $this->localeSecond);
                        $subscriptions = $this->getCurlData(
                            $baseUrl . '/' . $itemId . '/subscriptions',
                            $this->localePrimary
                        );

                        if ($this->withDebug) {
                            $dbTimer = microtime(true);
                        }

                        $localities = [];

                        if (!empty($item->service_areas)) {
                            foreach ($item->service_areas as $locality) {
                                // in API data not unique
                                $localities[$locality->locality] = $locality;
                            }
                        }

                        if (empty($item->radius_served)) {
                            $radius = BusinessProfile::DEFAULT_MILES_FROM_MY_BUSINESS;
                        } else {
                            $radius = $item->radius_served;
                        }

                        if ($this->twitterOnly) {
                            $this->updateTwitter($businessProfile, $itemPrimary->business->profile);
                        } else {
                            $this->addBusinessProfileByApiData(
                                $itemPrimary,
                                $itemSecond,
                                $subscriptions,
                                $localities,
                                $radius,
                                $country
                            );
                        }

                        if ($this->withDebug) {
                            $curlInterval = $dbTimer - $curlTimer;
                            $dbInterval = microtime(true) - $dbTimer;

                            $pageCurlTime += $curlInterval;
                            $pageDbTime += $dbInterval;

                            $output->writeln('Curl Timer: ' . $curlInterval . '; DB Timer: ' . $dbInterval);
                            $output->writeln('Finish request item with id ' . $itemId);
                        }
                    } else {
                        if ($this->withDebug) {
                            $output->writeln('Skip as existed item with id ' . $itemId);
                        }
                    }
                }
            }

            $this->em->flush();
            $this->em->clear();
        }

        if ($this->withDebug) {
            $output->writeln('Total time: '.(microtime(true) - $this->totalTimer));
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

    private function addBusinessProfileByApiData(
        $itemPrimary,
        $itemSecond,
        $subscriptions,
        $localities,
        $radius,
        $country
    ) {
        $manager = $this->getContainer()->get('domain_business.manager.business_profile');

        $business = $itemPrimary->business;

        $name = $business->name;

        $entity = $manager->createProfile();

        // populate profile

        $entity->setUid($business->id);
        $entity->setName(trim($name));

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

        $entity->setEmail(trim($profile->email));
        $entity->setSlogan(trim($profile->slogan));
        $entity->setDescription(trim($profile->description));
        $entity->setProduct(trim($profile->products));

        $workingHours = mb_substr(
            trim($profile->hours_opr),
            0,
            BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH
        );
        $entity->setWorkingHours($workingHours);

#        $entity->setPosition(?);

        $address = $business->address;

        $entity->setCity(trim($address->locality));
        $entity->setStreetAddress(trim($address->street_address));
        $entity->setExtendedAddress(trim($address->extended_address));
        $entity->setCrossStreet(trim($address->cross_street));
        $entity->setLongitude(trim($address->coordinates[0]));
        $entity->setLatitude(trim($address->coordinates[1]));

        $entity->setCountry($country);

#        $entity->setState(?);
#        $entity->setStreetNumber(?);
#        $entity->setUseMapAddress(?);

#        $entity->setGoogleAddress(?);

        $entity->setFacebookURL($this->handleUrl($profile->facebook_page_url));
        $entity->setWebsite($this->handleUrl($profile->website));

        $entity->setGoogleURL($this->handleUrl($profile->google_plus_url));
        $entity->setYoutubeURL($this->handleUrl($profile->yt_url));

        $entity = $this->updateTwitter($entity, $profile);

        // process assigned items

        if (!$this->skipImages and $profile->images) {
            $managerGallery = $this->getContainer()->get('domain_business.manager.business_gallery');
            $container = $this->getContainer();

            $pathWeb = $container->get('kernel')->getRootDir() .
                $container->getParameter('image_back_up_path') .
                SiteHelper::generateBusinessSubfolder($business->id);

            foreach ($profile->images as $image) {
                $context = ($image->label == 'logo') ?
                    OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO :
                    OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES;

                $path = $pathWeb . substr($image->image->url, strrpos($image->image->url, '/'));

                /*
                 * Migration page 2834
                 * [Imagine\Exception\RuntimeException]
                 * An image could not be created from the given input
                 */

                if (file_exists($path)) {
                    $managerGallery->createNewEntryFromLocalFile($entity, $context, $path);
                } else {
                    $path = 'http://assets3.drxlive.com' . $image->image->url;

                    $managerGallery->createNewEntryFromRemoteFile($entity, $context, $path);
                }
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

        if ($address->postal_code and $this->checkZipCode($address->postal_code)) {
            $entity->setZipCode(trim($address->postal_code));
        } elseif ($entity->getCatalogLocality()) {
            $neighborhoods = $entity->getCatalogLocality()->getNeighborhoods();

            if (!$neighborhoods->isEmpty()) {
                $zipCodes = $neighborhoods->first()->getZips();

                if (!$zipCodes->isEmpty()) {
                    $entity->setZipCode($zipCodes->first()->getZipCode());
                }
            }
        }

        if ($profile->headings) {
            //add categories
            foreach ($profile->headings as $value) {
                $category = $this->getCategory($value);
                $entity = $this->addBusinessProfileCategory($entity, $category);
            }
        }

        if ($entity->getCategories()->isEmpty()) {
            //add undefined categories
            $category = $this->getDefaultCategory();
            $entity = $this->addBusinessProfileCategory($entity, $category);
        }

        if ($business->phones) {
            $phones = [];

            foreach ($business->phones as $item) {
                // in API data not unique
                $phones[$item->number] = $item->number;
            }

            foreach ($phones as $item) {
                if (mb_strlen($item) <= BusinessProfilePhone::MAX_PHONE_LENGTH) {
                    $phone = new BusinessProfilePhone();
                    $phone->setPhone($item);

                    $entity->addPhone($phone);
                }
            }
        }

        if ($profile->tags) {
            foreach ($profile->tags as $item) {
                foreach ($this->splitTags($item) as $tag) {
                    $tag = mb_substr(trim($tag), 0, Tag::TAG_NAME_MAX_LENGTH);

                    $newTag = $this->loadTag($tag);

                    if (!$entity->getTags()->contains($newTag)) {
                        $entity->addTag($newTag);
                    }
                }
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
                $brandPrimary = mb_substr(
                    implode(PHP_EOL, $profile->brands),
                    0,
                    BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS_LENGTH
                );
            }

            if ($profileSecond->brands) {
                $brandSecond = mb_substr(
                    implode(PHP_EOL, $profileSecond->brands),
                    0,
                    BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS_LENGTH
                );
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
                    } else {
                        $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_EXPIRED);
                    }

                    $entity->addSubscription($subscription);
                } else {
                    if ($this->withDebug) {
                        $this->output->writeln('Unknown subscription Plan:' . json_encode($item));
                    }
                }
            }
        }

        $subscriptionManager = $this->getContainer()->get('domain_business.manager.subscription_status_manager');
        $entity = $subscriptionManager->updateBusinessProfileFreeSubscription($entity, $this->em);

        $seoTitle       = BusinessProfileUtil::seoTitleBuilder($entity, $this->getContainer());
        $seoDescription = BusinessProfileUtil::seoDescriptionBuilder($entity, $this->getContainer());

        $seoTitle       = mb_convert_encoding($seoTitle, 'UTF-8');
        $seoDescription = mb_convert_encoding($seoDescription, 'UTF-8');

        $entity->setSeoTitle($seoTitle);
        $entity->setSeoDescription($seoDescription);

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

        $entity->setNameEn(trim($nameEn));
        $entity->setNameEs(trim($nameEs));

        $entity->setDescriptionEn(trim($descriptionEn));
        $entity->setDescriptionEs(trim($descriptionEs));

        $this->em->persist($entity);

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
    }

    private function getCategory($name)
    {
        $slug = SlugUtil::convertSlug($name);

        $entity = $this->em->getRepository(Category::class)->getCategoryByOldSlugs($slug);

        if (!$entity) {
            $newSlug = '';

            if (!empty($this->categoryEnMergeMapping[$slug])) {
                $newSlug = $this->categoryEnMergeMapping[$slug];
            } elseif (!empty($this->categoryEsMergeMapping[$slug])) {
                $newSlug = $this->categoryEsMergeMapping[$slug];
            }

            if ($newSlug) {
                $entity = $this->em->getRepository(Category::class)->getCategoryByOldSlugs($newSlug);
            }
        }

        return $entity;
    }

    private function getDefaultCategory()
    {
        $slug = Category::CATEGORY_UNDEFINED_SLUG;
        $entity = $this->em->getRepository(Category::class)->findOneBy([
            'slug' => $slug,
        ]);

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

        $entity = $repository->getLocalityBySlug(SlugUtil::convertSlug(trim($item->locality)));

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

    /**
     * @param BusinessProfile $businessProfile
     * @param Category|null   $category
     *
     * @return BusinessProfile
     */
    private function addBusinessProfileCategory($businessProfile, $category)
    {
        if ($category and !$businessProfile->getCategories()->contains($category)) {
            $businessProfile->addCategory($category);
        }

        return $businessProfile;
    }

    private function checkZipCode($zipCode)
    {
        if (mb_strlen(trim($zipCode)) > BusinessProfile::BUSINESS_PROFILE_ZIP_MAX_LENGTH) {
            return false;
        }

        return true;
    }

    private function splitTags($value)
    {
        return explode(self::TAG_SEPARATOR, $value);
    }

    private function handleUrl($url)
    {
        $url = trim($url);

        if (mb_strlen($url) > BusinessProfile::BUSINESS_PROFILE_URL_MAX_LENGTH) {
            return null;
        }

        return $url;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param mixed           $profile
     *
     * @return BusinessProfile
     */
    private function updateTwitter($businessProfile, $profile)
    {
        if (trim($profile->twitter_handle)) {
            $twitterUrl = $this->handleUrl(self::TWITTER_URL_PREFIX . trim($profile->twitter_handle));
            $businessProfile->setTwitterURL($twitterUrl);
        }

        return $businessProfile;
    }
}
