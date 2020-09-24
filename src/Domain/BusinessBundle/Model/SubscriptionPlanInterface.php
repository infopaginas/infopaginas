<?php

namespace Domain\BusinessBundle\Model;

/**
 * Class SubscriptionPlanInterface
 * @package Domain\BusinessBundle\Model
 */
interface SubscriptionPlanInterface
{
    public const CODE_FREE             = 1;
    public const CODE_PRIORITY         = 2;
    public const CODE_PREMIUM_PLUS     = 3;
    public const CODE_PREMIUM_GOLD     = 4;
    public const CODE_PREMIUM_PLATINUM = 5;
    public const CODE_SUPER_VM         = 6;

    public const CODE_SUPER_VM_CLASS   = 'super_vm';

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @return mixed
     */
    public function getCodeValue();

    /**
     * @return mixed
     */
    public static function getCodes();
}
