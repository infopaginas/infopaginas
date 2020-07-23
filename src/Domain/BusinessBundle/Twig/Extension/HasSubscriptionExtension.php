<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ConfigExtension
 * @package Oxa\ConfigBundle\Twig\Extension
 */
class HasSubscriptionExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'hasSubscription',
                [
                    $this,
                    'isSubscribed',
                ],
                [
                    'needs_environment' => true,
                    'is_safe'           => [
                        'all',
                    ],
                ]
            ),
            new TwigFunction(
                'getItemSubscriptionClass',
                [
                    $this,
                    'getItemSubscriptionClass',
                ],
                [
                    'needs_environment' => true,
                    'is_safe'           => [
                        'all',
                    ],
                ]
            ),
        ];
    }

    public function isSubscribed($env, BusinessProfile $profile, $subscriptionPlan)
    {
        $rank = $profile->getSubscriptionPlanCode();

        try {
            // Suddenly, but SubscriptionPlanInterface::$subscriptionPlan does not works о_О
            $plan = (new \ReflectionClass('Domain\BusinessBundle\Entity\SubscriptionPlan'))
                ->getConstant($subscriptionPlan);

            return $rank >= $plan;
        } catch (Exception $e) {
            throw new Exception(
                sprintf("Subscription plan '%s' does not exists", $subscriptionPlan),
                1
            );
        }
    }

    public function getItemSubscriptionClass($env, BusinessProfile $profile)
    {
        $code = $profile->getSubscriptionPlanCode();

        if ($profile->getIsAd()) {
            $class = SubscriptionPlan::getSuperVmAdsClass();
        } else {
            $class = SubscriptionPlan::getCodeValues()[$code];
        }

        return $class;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'hasSubscription';
    }
}
