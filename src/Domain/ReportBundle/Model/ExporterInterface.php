<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/14/16
 * Time: 3:48 PM
 */

namespace Domain\ReportBundle\Model;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * Interface ExporterInterface
 * @package Domain\ReportBundle\Model
 */
interface ExporterInterface
{
    public function getResponse(string $code, string $format, array $objects, $parameters = []) : Response;
}
