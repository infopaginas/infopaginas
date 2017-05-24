<?php

namespace Domain\ReportBundle\Model;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ExporterInterface
 * @package Domain\ReportBundle\Model
 */
interface ExporterInterface
{
    public function getResponse($parameters = []) : Response;
}
