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

//     todo move to separate file
    /**
     * Delete action.
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function exportPreviewAction()
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('export', $object);
//        $adminManager = $this->get('oxa.sonata.manager.admin_manager');
        $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_LAST_MONTH);

        $chartList = BusinessOverviewModel::getChartEventTypesWithTranslation();

        $chartList[BusinessOverviewModel::TYPE_CODE_KEYWORD] = 'Keyword Limit';
        $chartList[BusinessOverviewModel::TYPE_CODE_ADS] = 'Ads';

        $interactionOptions = [
            'format' => BusinessProfileAdmin::DATE_PICKER_REPORT_FORMAT,
            'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
            'dateEnd'   => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
            'periodChoices' => DatesUtil::getReportAdminDataRanges(),
            'periodData' => DatesUtil::RANGE_LAST_MONTH,
            'choices'   => AdminHelper::getPeriodOptionValues(),
            'data'      => AdminHelper::PERIOD_OPTION_CODE_PER_MONTH,
            'limitChoices' => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
            'limitData' => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
        ];

        $interactionCharts = [];

        foreach ($chartList as $key => $label) {
            $interactionCharts[$key] = [
                'name' => $key,
                'label' => $label,
                'translationDomain' => null,
                'options' => $interactionOptions,
                'hasLimit' => false,
                'isAds' => false,
            ];
        }

        $interactionCharts[BusinessOverviewModel::TYPE_CODE_KEYWORD]['hasLimit'] = true;
        $interactionCharts[BusinessOverviewModel::TYPE_CODE_ADS]['isAds'] = true;

        return $this->render(
            'OxaSonataAdminBundle:CRUD:export_preview_pdf.html.twig',
            [
                'object' => $object,
                'interactionCharts' => $interactionCharts,
                'action' => 'exportPreview',
                'csrf_token' => $this->getCsrfToken('sonata.delete'),
            ]
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
