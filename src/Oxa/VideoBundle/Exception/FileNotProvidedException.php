<?php

namespace Oxa\VideoBundle\Exception;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 13:10
 */
class FileNotProvidedException extends \Exception
{
    const MESSAGE = 'You need to provide file path. Use "file" as data array key';

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        return parent::__construct(self::MESSAGE, $code, $previous);
    }
}
