<?php

namespace Domain\PageBundle\Model;

/**
 * Interface pageInterface
 * @package Domain\PageBundle\Model
 */
interface PageInterface
{
    const CODE_DEFAULT              = 0;
    const CODE_LANDING              = 1;
    const CODE_CONTACT_US           = 2;
    const CODE_PRIVACY_STATEMENT    = 3;
    const CODE_TERMS_OF_USE         = 4;
    const CODE_ADVERTISE            = 5;

    const CODE_ARTICLE_LIST                 = 10;
    const CODE_ARTICLE_CATEGORY_LIST        = 11;
    const CODE_CATALOG                      = 12;
    const CODE_CATALOG_LOCALITY             = 13;
    const CODE_CATALOG_LOCALITY_CATEGORY    = 14;
    const CODE_EMERGENCY                    = 15;
    const CODE_EMERGENCY_AREA_CATEGORY      = 16;

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param $code
     * @return mixed
     */
    public function setCode($code);
}
