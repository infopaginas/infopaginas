<?php

namespace Domain\MenuBundle\Model;

/**
 * Interface MenuInterface
 * @package Domain\MenuBundle\Model
 */
interface MenuInterface
{
    //Menu item
    const CODE_SOLICITORS       = 1;
    const CODE_BUILDERS         = 2;
    const CODE_PHARMACIES       = 3;
    const CODE_ELECTRICIANS     = 4;
    const CODE_PLUMBERS         = 5;
    const CODE_MECHANICS        = 6;
    const CODE_DENTISTS         = 7;
    const CODE_RESTAURANTS      = 8;
    const CODE_FLORISTS         = 9;
    const CODE_BEAUTY_SALONS    = 10;
    const CODE_HAIRDRESSERS     = 11;
    const CODE_DOCTORS          = 12;

    // other default categories
    const CODE_AGRICULTURE            = 100;
    const CODE_ARTS_AND_ENTERTAINMENT = 101;
    const CODE_AUTO                   = 102;
    const CODE_BEAUTY_AND_WELLNESS    = 103;
    const CODE_BUSINESS_SERVICES      = 104;
    const CODE_CLOTHING               = 105;
    const CODE_COMMUNICATION          = 106;
    const CODE_COMMUNITY              = 107;
    const CODE_CONSTRUCTION           = 108;
    const CODE_EDUCATION              = 109;
    const CODE_FINANCE                = 110;
    const CODE_HOUSE_AND_HOME         = 111;
    const CODE_INSURANCE              = 112;
    const CODE_JOBS                   = 113;
    const CODE_LAWN_AND_GARDEN        = 114;
    const CODE_LEGAL                  = 115;
    const CODE_MANUFACTURING          = 116;
    const CODE_MEDICAL                = 117;
    const CODE_NIGHTLIFE              = 118;
    const CODE_PETS_AND_ANIMALS       = 119;
    const CODE_PHOTO_AND_VIDEO        = 120;
    const CODE_PRINTING               = 121;
    const CODE_REAL_ESTATE            = 122;
    const CODE_RECREATION             = 123;
    const CODE_SERVICES               = 124;
    const CODE_SHOPPING               = 125;
    const CODE_STORAGE                = 126;
    const CODE_TECHNICAL              = 127;
    const CODE_TRANSPORTATION         = 128;
    const CODE_TRAVEL                 = 129;
    const CODE_UTILITIES              = 130;
    const CODE_WEDDING_AND_PARTY      = 131;
    const CODE_FLOWERS                = 132;
    const CODE_PHOTOGRAPHIC           = 133;
    const CODE_FUNERALS               = 134;
    const CODE_LAWYERS                = 135;
    const CODE_GARDENING              = 136;
    const CODE_BAKERY                 = 137;
    const CODE_BAKERIES               = 138;
    const CODE_BICYCLES               = 139;
    const CODE_CHURCHES               = 140;
    const CODE_CONSULTANTS            = 141;

    const CODE_UNDEFINED = 99999;

    /**
     * @return integer
     */
    public function getCode();

    /**
     * @param int $code
     * @return MenuInterface
     */
    public function setCode($code);
}
