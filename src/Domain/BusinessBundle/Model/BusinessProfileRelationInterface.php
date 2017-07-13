<?php

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
