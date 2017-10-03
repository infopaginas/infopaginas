<?php

namespace Domain\BusinessBundle\Form\Handler;

interface BusinessFormHandlerInterface
{
    const BUSINESS_NOT_FOUND_ERROR_MESSAGE = 'Business id is not found';

    const MESSAGE_BUSINESS_PROFILE_CLOSED  = 'business_profile.message.closed';
    const MESSAGE_BUSINESS_PROFILE_CREATED = 'business_profile.message.created';
    const MESSAGE_BUSINESS_PROFILE_UPDATED = 'business_profile.message.updated';
    const MESSAGE_BUSINESS_PROFILE_WELCOME = 'business_profile.message.welcome';

    const MESSAGE_EMERGENCY_BUSINESS_CREATED = 'emergency.business_draft.message.created';

    const MESSAGE_BUSINESS_PROFILE_FLASH_GROUP = 'success';
}
