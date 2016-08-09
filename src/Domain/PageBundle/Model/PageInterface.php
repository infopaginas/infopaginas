<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 6/02/16
 * Time: 11:44 AM
 */

namespace Domain\PageBundle\Model;

/**
 * Interface pageInterface
 * @package Domain\PageBundle\Model
 */
interface PageInterface
{
    const CODE_ABOUT_AS             = 1;
    const CODE_CONTACT_US           = 2;
    const CODE_PRIVACY_STATEMENT    = 3;
    const CODE_TERMS_OF_USE         = 4;

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
