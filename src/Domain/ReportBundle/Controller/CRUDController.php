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
    /**
     * Customize export action
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
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
                    'Export format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $format,
                    $this->admin->getClass(),
                    implode(', ', $allowedExportFormats)
                )
            );
        }

        if (!key_exists($code, $allowedExportFormats)) {
            throw new \RuntimeException(
                sprintf(
                    'Export code `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $code,
                    $this->admin->getClass(),
                    implode(', ', array_keys($allowedExportFormats))
                )
            );
        }
        
        // get pager params from request to have them in export services
        $parameters = array_merge(
            $this->admin->getFilterParameters(),
            [
                '_page' => $this->getRequest()->get('_page'),
                '_per_page' => $this->getRequest()->get('_per_page')
            ]
        );

        return $this->get('domain_report.exporter')->getResponse(
            $code,
            $format,
            $this->admin,
            $parameters
        );
    }
}
