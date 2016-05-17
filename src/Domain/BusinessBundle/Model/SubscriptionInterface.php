<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\BusinessBundle\Model;

/**
 * Class SubscriptionInterface
 * @package Domain\BusinessBundle\Model
 */
interface SubscriptionInterface
{
    const CODE_FREE             = 1;
    const CODE_PRIORITY         = 2;
    const CODE_PREMIUM_PLUS     = 3;
    const CODE_PREMIUM_GOLD     = 4;
    const CODE_PREMIUM_PLATINUM = 5;

//    public function getCode();
    
    public function getCodeValue();
    
    public static function getCodes();

}