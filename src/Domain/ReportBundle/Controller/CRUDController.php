<?php

namespace Domain\ReportBundle\Controller;

use \Oxa\Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CRUDController
 * @package Domain\ReportBundle\Controller
 */
class CRUDController extends Controller
{
    public function exportAction(Request $request)
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $format = $request->get('format');
        $code = $request->get('code');

        $allowedExportFormats = $this->admin->getExportFormats();

        if (!in_array($format, $allowedExportFormats)) {
            throw new \RuntimeException(
                sprintf(
                    'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $format,
                    $this->admin->getClass(),
                    implode(', ', $allowedExportFormats)
                )
            );
        }

        if (!key_exists($code, $allowedExportFormats)) {
            throw new \RuntimeException(
                sprintf(
                    'Export in code `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $code,
                    $this->admin->getClass(),
                    implode(', ', array_keys($allowedExportFormats))
                )
            );
        }

        return $this->get('domain_report.exporter')->getResponse(
            $code,
            $format,
            $this->admin
        );
    }

}