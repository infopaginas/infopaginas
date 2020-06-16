<?php

namespace Domain\BusinessBundle\Form\Handler;

interface BusinessFormHandlerInterface
{
    public const BUSINESS_NOT_FOUND_ERROR_MESSAGE = 'Business id is not found';

    public const MESSAGE_BUSINESS_PROFILE_CLOSED  = 'business_profile.message.closed';
    public const MESSAGE_BUSINESS_PROFILE_CREATED = 'business_profile.message.created';
    public const MESSAGE_BUSINESS_PROFILE_UPDATED = 'business_profile.message.updated';
    public const MESSAGE_BUSINESS_PROFILE_WELCOME = 'business_profile.message.welcome';

    public const MESSAGE_EMERGENCY_BUSINESS_CREATED = 'emergency.business_draft.message.created';

    public const MESSAGE_BUSINESS_PROFILE_FLASH_GROUP = 'success';
    public const SUCCESSFUL_REGISTRATION_TEXT_KEY     = 'registration_success';

    public const UNIQUE_PHONE_VALIDATION_GROUP = 'UniquePhone';
}
