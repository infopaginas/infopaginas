<?php

namespace Domain\MenuBundle\Model;

/**
 * Interface MenuInterface
 * @package Domain\MenuBundle\Model
 */
interface MenuInterface
{
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
