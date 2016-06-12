<?php

namespace Oxa\WistiaBundle\Exception;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 13:10
 */
class URLNotProvidedException extends \Exception
{
    const MESSAGE = 'You need to provide video URL. Use "url" as data array key';

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        return parent::__construct(self::MESSAGE, $code, $previous);
    }
}