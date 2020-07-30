<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\DBAL\Types\UrlType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\Task\ImagesChangeSetUtil;
use Domain\BusinessBundle\Util\Task\NormalizerUtil;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use JMS\Serializer\SerializerBuilder;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class BusinessProfilesChangesetExtension
 * @package Domain\BusinessBundle\Twig\Extension
 */
class BusinessProfileExtension extends AbstractExtension
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
    public function setBusinessProfileManager(BusinessProfileManager $businessProfileManager)
    {
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
            new TwigFunction('get_business_profiles_changeset_array', [$this, 'deserializeChangeSet']),
            new TwigFunction('media_tab_allowed_for_business', [$this, 'mediaTabAllowedForBusiness']),
            new TwigFunction('get_business_profile_by_uid', [$this, 'getBusinessProfileByUid']),
            new TwigFunction('get_business_profile_reviews_count', [$this, 'getBusinessProfileActualReviewsCount']),
            new TwigFunction('get_business_profile_reviews_avg_rating', [$this, 'getBusinessProfileActualReviewsAvgRating']),
            new TwigFunction('get_business_profile_changes_string', [$this, 'unpackTaskChangeSetRow']),
            new TwigFunction('get_business_profile_translation_changes', [$this, 'getTaskTranslationChangeSetRow']),
            new TwigFunction('get_business_profile_image_property_changes', [$this, 'getTaskImagePropertyChangeSetRow']),
            new TwigFunction('get_business_profile_phone_icon', [$this, 'getBusinessProfileIcon']),
            new TwigFunction('get_business_profile_media_changes', [$this, 'getMediaChangeSet']),
            new TwigFunction('get_business_profile_images_changes', [$this, 'getImagesChangeSet']),
            new TwigFunction('normalize_task_changeaction_label', [$this, 'normalizeTaskChangeActionLabel']),
            new TwigFunction('normalize_task_fieldname_label', [$this, 'normalizeTaskFieldNameLabel']),
            new TwigFunction('video_section_allowed_for_business', [$this, 'videoSectionAllowedForBusiness']),
            new TwigFunction(
                'render_task_media_link',
                [
                    $this,
                    'renderTaskMediaLink'
                ],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new TwigFunction(
                'render_task_images_link',
                [
                    $this,
                    'renderTaskImagesLink'
                ],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new TwigFunction(
                'get_business_profile_related_entity_changes_html',
                [
                    $this,
                    'renderBusinessProfileRelatedEntityChanges'
                ],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new TwigFunction(
                'get_business_profile_url_changes_html',
                [
                    $this,
                    'renderBusinessProfileUrlChanges'
                ],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new TwigFunction('get_business_profile_open_status', [$this, 'getBusinessProfileOpenStatus']),
            new TwigFunction('get_business_profile_working_hours_list', [$this, 'getBusinessProfileWorkingHoursList']),
            new TwigFunction(
                'get_business_profile_many_to_one_relations_changes_string',
                [
                    $this,
                    'unpackManyToOneRelationsChangeSetRow'
                ]
            ),
            new TwigFunction('get_business_profile_translation_changes_string', [$this, 'unpackTranslationChangeSetRow']),
            new TwigFunction(
                'get_wysiwyg_preview_block',
                [
                    $this,
                    'renderWysiwygPreviewForm'
                ],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new TwigFunction('get_business_profile_categories_json', [$this, 'getBusinessProfileCategoriesJson']),
            new TwigFunction('get_business_gallery_media', [$this, 'getBusinessGalleryMedia']),
            new TwigFunction('get_business_profile_phones_json', [$this, 'getBusinessProfilePhonesJson']),
            new TwigFunction('get_business_profile_markers', [$this, 'getBusinessProfileMarkers']),
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('international_phone', [$this, 'getInternationalPhone']),
        ];
    }

    /**
     * @param int $id
     * @return Media|null
     */
    public function getBusinessGalleryMedia($id)
    {
        return $this->getBusinessProfileManager()->getBusinessGalleryMediaById($id);
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
        if ($businessProfile) {
            $code = $businessProfile->getSubscriptionPlanCode();

            if ($code >= SubscriptionPlanInterface::CODE_PREMIUM_PLUS) {
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

        return implode(', ', array_map(function ($element) {
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

    /**
     * @param BusinessProfilePhone $phone
     *
     * @return string
     */
    public function getBusinessProfileIcon($phone)
    {
        $phoneIcons = BusinessProfilePhone::getTypeIcons();
        $type = $phone->getType();

        if (!array_key_exists($type, $phoneIcons)) {
            $type = BusinessProfilePhone::PHONE_TYPE_SECONDARY;
        }

        return $phoneIcons[$type];
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

    public function renderTaskMediaLink(Environment $environment, $data)
    {
        $html = $environment->render(
            ':redesign/blocks/task:task_media_link.html.twig',
            [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function renderTaskImagesLink(Environment $environment, $data)
    {
        $html = $environment->render(
            ':redesign/blocks/task:task_images_link.html.twig',
            [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function renderBusinessProfileRelatedEntityChanges(Environment $environment, $json)
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
            ':redesign/blocks/task:related_entity_changes.html.twig',
            [
                'data' => $data,
            ]
        );

        return $html;
    }

    public function renderBusinessProfileUrlChanges(Environment $environment, $json)
    {
        $data = [];
        $properties = json_decode($json);
        $key = 'value';

        if ($properties) {
            foreach ($properties as $item => $value) {
                switch ($item) {
                    case UrlType::URL_NAME:
                        $name = 'Url';

                        break;
                    case UrlType::REL_NO_FOLLOW:
                        $name = 'No Follow';

                        break;
                    case UrlType::REL_NO_REFERRER:
                        $name = 'No Referrer';

                        break;
                    case UrlType::REL_NO_OPENER:
                        $name = 'No opener';

                        break;
                    case UrlType::REL_SPONSORED:
                        $name = 'Sponsored';

                        break;
                    case UrlType::REL_UGC:
                        $name = 'User Generated Content';

                        break;
                    default:
                        $name = $item;

                        break;

                }

                $data[$key][$name] = $value;
            }
        }

        $html = $environment->render(
            ':redesign/blocks/task:related_entity_changes.html.twig',
            [
                'data' => $data,
            ]
        );

        return $html;
    }

    /**
     * @param Environment $environment
     * @param string $name
     * @param string $raw
     * @return string
     */
    public function renderWysiwygPreviewForm(Environment $environment, $name, $raw)
    {
        $form = $this->businessProfileManager->getWysiwygPreviewForm($name, $raw);

        $html = $environment->render(
            'DomainBusinessBundle:TaskAdmin/fields:wysiwig_field.html.twig',
            [
                'form' => $form->createView(),
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
                if ($workingHourData['hours']->openAllTime) {
                    $text = $this->translator->trans('business.working.hours.open_all_time');
                } else {
                    $endTime = $workingHourData['hours']->timeEnd
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

    /**
     * @param BusinessProfile|EmergencyBusiness
     *
     * @return \stdClass
     */
    public function getBusinessProfileWorkingHoursList($businessProfile)
    {
        $workingHourData = DayOfWeekModel::getBusinessProfileWorkingHoursListFEView($businessProfile);

        return $workingHourData;
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

    /**
     * @param BusinessProfile|null  $businessProfile
     * @param string                $locale
     *
     * @return string
     */
    public function getBusinessProfileCategoriesJson($businessProfile, $locale)
    {
        $categoriesData = [];

        if ($businessProfile) {
            $localePostfix = LocaleHelper::getLangPostfix($locale);

            foreach ($businessProfile->getCategories() as $category) {
                $categoriesData[$category->getId()] = [
                    'id'    => $category->getId(),
                    'name'  => $category->{'getSearchText' . $localePostfix}(),
                ];
            }
        }

        return json_encode($categoriesData);
    }

    /**
     * @param string $changeSetEntryValue
     *
     * @return false|string
     */
    public function getBusinessProfilePhonesJson(string $changeSetEntryValue)
    {
        if (!$this->isJson($changeSetEntryValue)) {
            return $changeSetEntryValue;
        }

        $data = [];
        foreach (json_decode($changeSetEntryValue) as $phone) {
            $data[] = json_decode($phone->value)->value;
        }

        return json_encode($data);
    }

    public function getBusinessProfileMarkers(Task $task)
    {
        $bp = $task->getBusinessProfile();

        if ($task->getType() == TaskType::TASK_PROFILE_UPDATE) {
            /** @var ChangeSet $changeSet */
            $changeSet = $task->getChangeSet();
            $changeSetEntries = $changeSet->getEntries();

            /** @var ChangeSetEntry $entry */
            foreach ($changeSetEntries as $entry) {
                switch ($entry->getFieldName()) {
                    case BusinessProfile::BUSINESS_PROFILE_FIELD_LONGITUDE:
                        $oldLongitude = $entry->getNewValue();
                        break;
                    case BusinessProfile::BUSINESS_PROFILE_FIELD_LATITUDE:
                        $oldLatitude = $entry->getNewValue();
                        break;
                }
            }

            if (!empty($oldLongitude) || !empty($oldLatitude)) {
                $markers[] = [
                    'name'      => $bp->getName(),
                    'latitude'  => $oldLatitude ?? $bp->getLatitude(),
                    'longitude' => $oldLongitude ?? $bp->getLongitude(),
                ];
            }
        }
        $markers[] = [
            'name'      => $bp->getName(),
            'latitude'  => $bp->getLatitude(),
            'longitude' => $bp->getLongitude(),
        ];

        return json_encode($markers);
    }

    public function getInternationalPhone($phone)
    {
        return $this->getBusinessProfileManager()->getInternationalPhoneNumber($phone);
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
