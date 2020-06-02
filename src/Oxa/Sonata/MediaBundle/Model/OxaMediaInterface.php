<?php

namespace Oxa\Sonata\MediaBundle\Model;

/**
 * Class OxaMediaInterface
 * @package Oxa\Sonata\MediaBundle\Model
 */
interface OxaMediaInterface
{
    public const PROVIDER_IMAGE    = 'sonata.media.provider.image';
    public const PROVIDER_FILE     = 'sonata.media.provider.file';

    public const CONTEXT_DEFAULT                     = 'default';
    public const CONTEXT_BUSINESS_PROFILE_IMAGES     = 'business_profile_images';
    public const CONTEXT_BUSINESS_PROFILE_LOGO       = 'business_profile_logo';
    public const CONTEXT_BUSINESS_PROFILE_BACKGROUND = 'business_profile_background';
    public const CONTEXT_COUPON                      = 'coupon';
    public const CONTEXT_ARTICLE                     = 'article';
    public const CONTEXT_PAGE_BACKGROUND             = 'page_background';
    public const CONTEXT_ARTICLE_IMAGES              = 'article_images';
    public const CONTEXT_VIDEO_POSTER                = 'video_poster';
    public const CONTEXT_HOMEPAGE_CAROUSEL           = 'homepage_carousel';
    public const CONTEXT_PAYMENT_METHOD              = 'payment_method';
    public const CONTEXT_TESTIMONIAL                 = 'testimonial';

    /**
     * @return mixed
     */
    public static function getContexts() : array;

    /**
     * @return mixed
     */
    public static function getProviders() : array;
}
