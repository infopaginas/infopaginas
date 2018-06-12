<?php

namespace Domain\ReportBundle\Controller;

use Domain\BusinessBundle\Admin\BusinessProfileAdmin;
use Domain\ReportBundle\Entity\ExportReport;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\PostponeExportInterface;
use Domain\ReportBundle\Util\DatesUtil;
use \Oxa\Sonata\AdminBundle\Controller\CRUDController as Controller;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $format      = $request->get('format');
        $entityClass = $this->admin->getClass();
        $params      = $request->query->all();

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

        $preResponse = $this->preExport($request, $this->admin->getSubject());
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($this->admin->getNewInstance() instanceof PostponeExportInterface) {
            return $this->createPostponeExportReport($entityClass, $format, $params);
        }

        return $this->get('domain_report.exporter')->getResponse(
            $entityClass,
            $format,
            $params
        );
    }

    /**
     * @param string $entityClass
     * @param string $format
     * @param array  $parameters
     *
     * @return RedirectResponse
     */
    protected function createPostponeExportReport($entityClass, $format, $parameters = [])
    {
        try {
            $this->addPostponeExportReport($entityClass, $format, $parameters);

            $this->addFlash(
                'sonata_flash_success',
                $this->trans('flash_export_report_requested', [], 'AdminReportBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('flash_export_report_request_error', [], 'AdminReportBundle')
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param string $entityClass
     * @param string $format
     * @param array  $parameters
     */
    protected function addPostponeExportReport($entityClass, $format, $parameters = [])
    {
        $exportReport = new ExportReport();

        $exportReport->setFormat($format);
        $exportReport->setClass($entityClass);

        $exportReport->setParams($parameters);

        $user = $this->getUser();
        if ($user) {
            $exportReport->setUser($user);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($exportReport);
        $em->flush();
    }
}
