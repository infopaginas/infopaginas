<?php

namespace Domain\PageBundle\Model;

/**
 * Interface pageInterface
 * @package Domain\PageBundle\Model
 */
interface PageInterface
{
    const CODE_LANDING              = 1;
    const CODE_CONTACT_US           = 2;
    const CODE_PRIVACY_STATEMENT    = 3;
    const CODE_TERMS_OF_USE         = 4;
    const CODE_ADVERTISE            = 5;

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
