<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.07.16
 * Time: 12:05
 */

namespace Domain\BusinessBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\Task\ImagesChangeSetUtil;
use Domain\BusinessBundle\Util\Task\NormalizerUtil;
use Gedmo\Translatable\TranslatableListener;
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
            'get_business_profile_image_changes' => new \Twig_Function_Method($this, 'getImageChangeSet'),
            'prepare_image_diff' => new \Twig_Function_Method($this, 'prepareImageDiff'),
            'normalize_task_changeaction_label' => new \Twig_Function_Method($this, 'normalizeTaskChangeActionLabel'),
            'normalize_task_fieldname_label' => new \Twig_Function_Method($this, 'normalizeTaskFieldNameLabel'),
            'video_section_allowed_for_business' => new \Twig_Function_Method($this, 'videoSectionAllowedForBusiness'),
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
            $isGoldPlan     = $subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_GOLD;
            $isPlatinumPlan = $subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM;

            if ($isGoldPlan || $isPlatinumPlan) {
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

    public function getImageChangeSet(string $value)
    {
        return ImagesChangeSetUtil::deserializeChangeSet($value);
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

            if ($subscription->getCode() === SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
                return true;
            }
        }

        return false;
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
        $result = json_decode($json);

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
