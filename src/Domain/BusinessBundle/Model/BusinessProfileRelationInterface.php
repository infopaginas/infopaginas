<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\BusinessBundle\Model;

/**
 * Class BusinessProfileRelationInterface
 * @package Domain\BusinessBundle\Model
 */
interface BusinessProfileRelationInterface
{
    const PROPERTY_NAME_BUSINESS_PROFILE    = 'businessProfile';

    /**
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile();
}
