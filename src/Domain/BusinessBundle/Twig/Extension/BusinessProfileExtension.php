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
use Gedmo\Translatable\TranslatableListener;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormInterface;

/**
 * Class BusinessProfilesChangesetExtension
 * @package Domain\BusinessBundle\Twig\Extension
 */
class BusinessProfileExtension extends \Twig_Extension
{
    /** @var BusinessProfileManager */
    private $businessProfileManager;

    public function setBusinessProfileManager(BusinessProfileManager $businessProfileManager)
    {
        $this->businessProfileManager = $businessProfileManager;
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

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'business_profile_extension';
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }
}
