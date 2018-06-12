<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Admin\BusinessProfileAdmin;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use \Oxa\Sonata\AdminBundle\Controller\CRUDController as Controller;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Class CRUDController
 * @package Domain\ReportBundle\Controller
 */
class BusinessProfileCRUDController extends Controller
{
    /**
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function exportPreviewAction()
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('export', $object);

        $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_LAST_MONTH);

        $chartList = BusinessOverviewModel::getChartEventTypesWithTranslation();

        $chartList[BusinessOverviewModel::TYPE_CODE_KEYWORD] = 'Keyword Limit';
        $chartList[BusinessOverviewModel::TYPE_CODE_ADS] = 'Ads';

        $interactionOptions = [
            'format' => BusinessProfileAdmin::DATE_PICKER_REPORT_FORMAT,
            'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
            'dateEnd' => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
            'periodChoices' => DatesUtil::getReportAdminDataRanges(),
            'periodData' => DatesUtil::RANGE_LAST_MONTH,
            'choices' => AdminHelper::getPeriodOptionValues(),
            'data' => AdminHelper::PERIOD_OPTION_CODE_PER_MONTH,
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

}