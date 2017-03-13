<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.07.16
 * Time: 12:05
 */

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\Task\ImagesChangeSetUtil;
use Domain\BusinessBundle\Util\Task\NormalizerUtil;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BusinessProfilesChangesetExtension
 * @package Domain\BusinessBundle\Twig\Extension
 */
class BusinessProfileExtension extends \Twig_Extension
{
    /** @var BusinessProfileManager */
    private $businessProfileManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var FormInterface */
    private $form;

    /**
     * @param BusinessProfileManager $businessProfileManager
     */
    public function setBusinessProfileManager(BusinessProfileManager $businessProfileManager) {
        $this->businessProfileManager = $businessProfileManager;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormInterface $form
     */
    public function setBusinessProfileForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'get_business_profiles_changeset_array' => new \Twig_Function_Method($this, 'deserializeChangeSet'),
            'media_tab_allowed_for_business' => new \Twig_Function_Method($this, 'mediaTabAllowedForBusiness'),
            'get_business_profile_by_uid' => new \Twig_Function_Method($this, 'getBusinessProfileByUid'),
            'get_business_profile_reviews_count' => new \Twig_Function_Method(
                $this,
                'getBusinessProfileActualReviewsCount'
            ),
            'get_business_profile_reviews_avg_rating' => new \Twig_Function_Method(
                $this,
                'getBusinessProfileActualReviewsAvgRating'
            ),
            'get_business_profile_changes_string' => new \Twig_Function_Method($this, 'unpackTaskChangeSetRow'),
            'get_business_profile_translation_changes' => new \Twig_Function_Method(
                $this,
                'getTaskTranslationChangeSetRow'
            ),
            'get_business_profile_image_property_changes' => new \Twig_Function_Method(
                $this,
                'getTaskImagePropertyChangeSetRow'
            ),
            'get_business_profile_media_changes' => new \Twig_Function_Method($this, 'getMediaChangeSet'),
            'get_business_profile_images_changes' => new \Twig_Function_Method($this, 'getImagesChangeSet'),
            'prepare_image_diff' => new \Twig_Function_Method($this, 'prepareImageDiff'),
            'normalize_task_changeaction_label' => new \Twig_Function_Method($this, 'normalizeTaskChangeActionLabel'),
            'normalize_task_fieldname_label' => new \Twig_Function_Method($this, 'normalizeTaskFieldNameLabel'),
            'video_section_allowed_for_business' => new \Twig_Function_Method($this, 'videoSectionAllowedForBusiness'),
            'get_business_profile_images' => new \Twig_Function_Method($this, 'getBusinessProfileImages'),
            'get_business_profile_ads' => new \Twig_Function_Method($this, 'getBusinessProfileAds'),
            'render_task_media_link' => new \Twig_Function_Method(
                $this,
                'renderTaskMediaLink',
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            'render_task_images_link' => new \Twig_Function_Method(
                $this,
                'renderTaskImagesLink',
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            'get_business_profile_related_entity_changes_html' => new \Twig_Function_Method(
                $this,
                'renderBusinessProfileRelatedEntityChanges',
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            'get_business_profile_open_status' => new \Twig_Function_Method($this, 'getBusinessProfileOpenStatus'),
            'get_business_profile_working_hours_list' => new \Twig_Function_Method(
                $this,
                'getBusinessProfileWorkingHoursList'
            ),
            'get_business_profile_many_to_one_relations_changes_string' => new \Twig_Function_Method(
                $this,
                'unpackManyToOneRelationsChangeSetRow'
            ),
            'get_business_profile_translation_changes_string' => new \Twig_Function_Method(
                $this,
                'unpackTranslationChangeSetRow'
            ),
        ];
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return int
     */
    public function getBusinessProfileActualReviewsCount(BusinessProfile $businessProfile)
    {
        return $this->getBusinessProfileManager()->getReviewsCountForBusinessProfile($businessProfile);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return float|int
     */
    public function getBusinessProfileActualReviewsAvgRating(BusinessProfile $businessProfile)
    {
        return $this->getBusinessProfileManager()->calculateReviewsAvgRatingForBusinessProfile($businessProfile);
    }

    /**
     * @param string $changeSet
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function deserializeChangeSet(string $changeSet)
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->deserialize($changeSet, 'array', 'json');
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return bool
     */
    public function mediaTabAllowedForBusiness(BusinessProfile $businessProfile) : bool
    {
        if (!$businessProfile) {
            return false;
        }

        $subscription = $businessProfile->getSubscriptionPlan();

        if ($subscription) {
            $isPlusPlan     = $subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_PLUS;
            $isGoldPlan     = $subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_GOLD;
            $isPlatinumPlan = $subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM;
            $isSuperVm      = $subscription->getCode() === SubscriptionPlanInterface::CODE_SUPER_VM;

            if ($isPlusPlan || $isGoldPlan || $isPlatinumPlan || $isSuperVm) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $uid
     * @return BusinessProfile
     */
    public function getBusinessProfileByUid(string $uid) : BusinessProfile
    {
        $businessProfile = $this->getBusinessProfileManager()->findByUid($uid);
        return $businessProfile !== null ? $businessProfile : new BusinessProfile();
    }

    public function unpackTaskChangeSetRow($value)
    {
        if (!$this->isJson($value)) {
            return $value;
        }

        return implode(', ', array_map(function($element) {
            if (isset($element->value)) {
                return $element->value;
            } elseif (isset($element->url)) {
                    return $element->url;
            }

            return '';
        }, json_decode($value)));
    }

    public function unpackManyToOneRelationsChangeSetRow($value)
    {
        if ($this->isJson($value)) {
            $item = json_decode($value);

            if ($item->value) {
                return $item->value;
            }
        }

        return $value;
    }

    public function unpackTranslationChangeSetRow($value)
    {
        if ($this->isJson($value)) {
            $item = json_decode($value);

            if ($item->value and $item->locale) {
                return $item;
            }
        }

        return [];
    }

    public function getTaskTranslationChangeSetRow($oldValue, $newValue)
    {
        if (!$this->isJson($oldValue) or !$this->isJson($newValue)) {
            return [];
        }

        $oldData = $this->sortTranslationSet($oldValue);
        $newData = $this->sortTranslationSet($newValue);
        $data    = [];

        foreach ($oldData as $key => $item) {
            if (empty($newData[$key])) {
                $data[$key]['old'] = $item;
                $data[$key]['new'] = '';
            } else {
                if ($newData[$key] !== $item) {
                    $data[$key]['old'] = $item;
                    $data[$key]['new'] = $newData[$key];
                }
            }
        }

        foreach ($newData as $key => $item) {
            if (empty($oldData[$key])) {
                $data[$key]['old'] = '';
                $data[$key]['new'] = $newData[$key];
            }
        }

        return $data;
    }

    /**
     * Prepare BusinessGallery updated properties for view
     *
     * @param string $oldValue
     * @param string $newValue
     * @return array
     */
    public function getTaskImagePropertyChangeSetRow($oldValue, $newValue)
    {
        if (!$this->isJson($oldValue) or !$this->isJson($newValue)) {
            return [];
        }

        $oldData = $this->sortImagePropertySet($oldValue);
        $newData = $this->sortImagePropertySet($newValue);
        $data    = [];

        foreach ($oldData as $key => $item) {
            if ($newData[$key] !== $item) {
                $data[$key]['old'] = $item;
                $data[$key]['new'] = $newData[$key];
            }
        }

        return $data;
    }

    public function sortTranslationSet($value)
    {
        $translations = json_decode($value);
        $data = [];

        foreach ($translations as $translation) {
            $item = json_decode($translation->value);

            if ($item->field !== BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE and
                $item->field !== BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION) {
                $data[$item->field . $item->locale] = $item->field . ' [' . $item->locale . ']: ' . $item->value;
            }
        }

        ksort($data);

        return $data;
    }

    /**
     * Sort BusinessGallery updated properties
     *
     * @param string $value
     * @return array
     */
    public function sortImagePropertySet($value)
    {
        $properties = json_decode($value);
        $data = [];

        foreach ($properties as $raw) {
            $item = json_decode($raw->value);

            foreach ($item as $key => $field) {
                $data[$key] = ucfirst($key) . ' ' . $field;
            }
        }

        ksort($data);

        return $data;
    }

    public function getMediaChangeSet($value, $change)
    {
        $data = [];

        if ($value) {
            $data = ImagesChangeSetUtil::deserializeChangeSet($value);
            $data->url = $this->businessProfileManager->getTaskMediaLink($change, $value);
        }

        return $data;
    }

    public function getImagesChangeSet($value, $change)
    {
        $images = json_decode($value);
        $data = [];

        if ($images) {
            foreach ($images as $key => $item) {
                $data[$key] = $item;
                $data[$key]->url = $this->businessProfileManager->getTaskMediaLink($change, $item);
            }
        }

        return $data;
    }

    public function renderTaskMediaLink(\Twig_Environment $environment, $data)
    {
        $html = $environment->render(
            ':redesign/blocks/task:task_media_link.html.twig', [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function renderTaskImagesLink(\Twig_Environment $environment, $data)
    {
        $html = $environment->render(
            ':redesign/blocks/task:task_images_link.html.twig', [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function renderBusinessProfileRelatedEntityChanges(\Twig_Environment $environment, $json)
    {
        $data = [];
        $raw = json_decode($json);

        if ($raw) {
            foreach ($raw as $key => $item) {
                if ($this->isJson($item->value)) {
                    $property = json_decode($item->value);

                    foreach ($property as $name => $value) {
                        if (!empty($value->date)) {
                            $date = new \DateTime($value->date);

                            $data[$key][$name] = $date->format(BusinessProfileWorkingHour::DEFAULT_TASK_TIME_FORMAT);
                        } else {
                            $data[$key][$name] = $value;
                        }
                    }
                } else {
                    $data[$key]['value'] = $item->value;
                }
            }
        }

        $html = $environment->render(
            ':redesign/blocks/task:related_entity_changes.html.twig', [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function getBusinessProfileOpenStatus(BusinessProfile $businessProfile)
    {
        $workingHourData = DayOfWeekModel::getBusinessProfileOpenNowData($businessProfile);

        $text = '';

        if ($workingHourData['status']) {
            if ($workingHourData['hours']) {
                if ($workingHourData['hours']->getOpenAllTime()) {
                    $text = $this->translator->trans('business.working.hours.open_all_time');
                } else {
                    $endTime = $workingHourData['hours']->getTimeEnd()
                        ->format(BusinessProfileWorkingHour::DEFAULT_TASK_TIME_FORMAT);

                    $text = $this->translator->trans(
                        'business.working.hours.open_until',
                        [
                            '{-TIME-}' => $endTime,
                        ]
                    );
                }
            } else {
                $text = $this->translator->trans('business.working.hours.closed_now');
            }
        }

        $workingHourData['text'] = $text;

        return $workingHourData;
    }

    public function getBusinessProfileWorkingHoursList(BusinessProfile $businessProfile)
    {
        $workingHourData = DayOfWeekModel::getBusinessProfileWorkingHoursList($businessProfile);

        return $workingHourData;
    }

    public function prepareImageDiff($diff)
    {
        return ImagesChangeSetUtil::prepareImageDiff($diff);
    }

    public function normalizeTaskChangeActionLabel(string $action)
    {
        return NormalizerUtil::normalizeTaskChangeActionLabel($action, $this->getTranslator());
    }

    public function normalizeTaskFieldNameLabel(string $field)
    {
        return NormalizerUtil::normalizeTaskFieldNameLabel($field, $this->form);
    }

    public function videoSectionAllowedForBusiness(BusinessProfile $businessProfile) : bool
    {
        if (!$businessProfile) {
            return false;
        }

        $subscription = $businessProfile->getSubscriptionPlan();

        if ($subscription) {

            if ($subscription->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
                return true;
            }
        }

        return false;
    }

    public function getBusinessProfileImages(BusinessProfile $businessProfile)
    {
        $photos = $this->getBusinessProfileManager()->getBusinessProfilePhotoImages($businessProfile);

        return $photos;
    }

    public function getBusinessProfileAds(BusinessProfile $businessProfile)
    {
        $advertisements = $this->getBusinessProfileManager()->getBusinessProfileAdvertisementImages($businessProfile);

        return $advertisements;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return BusinessProfileManager
     */
    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }

    /**
     * @param $json
     * @return bool
     */
    private function isJson($json)
    {
        $result = json_decode($json, true);

        if (!is_array($result)) {
            return false;
        }

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'business_profile_extension';
    }
}
