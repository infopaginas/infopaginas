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
    const CODE_BOOKSTORES             = 142;
    const CODE_FURNITURE              = 143;
    const CODE_VIDEO_GAME             = 144;
    const CODE_VETERINARIANS          = 145;
    const CODE_SPORTS                 = 146;
    const CODE_ART                    = 147;
    const CODE_ASSOCIATIONS           = 148;
    const CODE_ASTROLOGY              = 149;
    const CODE_BANKS                  = 150;
    const CODE_BOATS                  = 151;
    const CODE_TRUCKS                 = 152;
    const CODE_COMPUTERS              = 153;
    const CODE_COPYING_MACHINES       = 154;
    const CODE_CANVAS_CURTAINS        = 155;
    const CODE_ELEVATORS              = 156;
    const CODE_EXTERMINATOR           = 157;
    const CODE_TOOLS                  = 158;
    const CODE_ICE                    = 159;
    const CODE_HOSPITALS              = 160;
    const CODE_HOTELS                 = 161;
    const CODE_PRINTERS               = 162;
    const CODE_INTERNET               = 163;
    const CODE_LABORATORIES           = 164;
    const CODE_LAUNDRIES              = 165;
    const CODE_CLEANING               = 166;
    const CODE_MACHINERY              = 167;
    const CODE_TYPEWRITERS            = 168;
    const CODE_MARINAS                = 169;
    const CODE_HOME                   = 170;
    const CODE_ENGINES                = 171;
    const CODE_PLUMBING               = 172;
    const CODE_DOORS                  = 173;
    const CODE_SIGNS                  = 174;
    const CODE_SECURITY               = 175;
    const CODE_UPHOLSTERERS           = 176;
    const CODE_CARDS                  = 177;
    const CODE_WINDOWS                = 178;
    const CODE_VIDEO                  = 179;
    const CODE_Fences                 = 180;
    const CODE_TRAILERS               = 181;
    const CODE_WATER                  = 182;
    const CODE_AIR_CONDITIONER        = 183;
    const CODE_JEWELERS               = 184;
    const CODE_THERAPY                = 185;
    const CODE_DIVERS                 = 186;
    const CODE_SWIMMING_POOLS         = 187;
    const CODE_PHYSICIANS_AND_SURGEONS = 188;
    const CODE_HANDBAGS                = 189;
    const CODE_UNIFORMS                = 190;
    const CODE_TELEVISION              = 191;
    const CODE_TELECOMMUNICATION       = 192;
    const CODE_SPRINKLERS              = 193;
    const CODE_SERVICE_STATIONS        = 194;
    const CODE_SCHOOLS                 = 195;
    const CODE_REFRIGERATING_EQUIPMENT = 196;
    const CODE_RADIO_COMMUNICATION     = 197;
    const CODE_PUMPS                   = 198;
    const CODE_PSYCHOLOGIST            = 199;
    const CODE_OPTICIANS               = 200;
    const CODE_MUSIC                   = 201;
    const CODE_HEAVY_EQUIPMENT         = 202;
    const CODE_GROCERY_STORE           = 203;
    const CODE_GAS                     = 204;
    const CODE_FISH_SEAFOOD            = 205;

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
