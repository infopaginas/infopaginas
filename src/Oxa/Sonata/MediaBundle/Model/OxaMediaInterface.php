<?php

namespace Oxa\Sonata\MediaBundle\Model;

/**
 * Class OxaMediaInterface
 * @package Oxa\Sonata\MediaBundle\Model
 */
interface OxaMediaInterface
{
    const PROVIDER_IMAGE    = 'sonata.media.provider.image';
    const PROVIDER_FILE     = 'sonata.media.provider.file';

    const CONTEXT_DEFAULT                     = 'default';
    const CONTEXT_BUSINESS_PROFILE_IMAGES     = 'business_profile_images';
    const CONTEXT_BUSINESS_PROFILE_LOGO       = 'business_profile_logo';
    const CONTEXT_BUSINESS_PROFILE_BACKGROUND = 'business_profile_background';
    const CONTEXT_COUPON                      = 'coupon';
    const CONTEXT_ARTICLE                     = 'article';
    const CONTEXT_PAGE_BACKGROUND             = 'page_background';
    const CONTEXT_ARTICLE_IMAGES              = 'article_images';
    const CONTEXT_VIDEO_POSTER                = 'video_poster';
    const CONTEXT_HOMEPAGE_CAROUSEL           = 'homepage_carousel';
    const CONTEXT_PAYMENT_METHOD              = 'payment_method';

    /**
     * @return mixed
     */
    public static function getContexts() : array;

    /**
     * @return mixed
     */
    public static function getProviders() : array;
}
