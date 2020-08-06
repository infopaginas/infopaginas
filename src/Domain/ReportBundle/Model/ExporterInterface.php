<?php

namespace Domain\ReportBundle\Model;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ExporterInterface
 * @package Domain\ReportBundle\Model
 */
interface ExporterInterface
{
    public const MAX_ROW_PER_FILE = 50000;

    /**
     * @param array
     */
    public function getResponse($parameters = []);
}
