<?php

namespace Domain\ReportBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\EqualType;

/**
 * Class InteractionsReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class InteractionsReportAdmin extends ReportAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('businessProfile')
            ->remove('date')
            ->add(
                'businessProfile', 'doctrine_orm_choice', [
                'field_type' => 'choice',
                'field_options' => [
                    'mapped' => false,
                    'required'  => true,
                    'empty_value'  => null,
                    'choices'   => $this->getDoctrine()->getRepository(BusinessProfile::class)
                        ->getBusinessProfilesForFilter(),
                    'translation_domain' => 'SonataAdminBundle',
                    'attr' => [],
                ],
            ])
            ->add('date', 'doctrine_orm_datetime_range', [
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'format' => AdminHelper::FILTER_DATE_RANGE_FORMAT,
                    'attr' => [
                        'class' => AdminHelper::FILTER_DATE_RANGE_CLASS
                    ],
                    'mapped' => false,
                    'required'  => true,
                    'empty_value'  => false,
                ]
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->interactionsData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.interactions')
            ->getInteractionsData($filterParam);

        $listMapper
            ->add('category', null, ['sortable' => false])
            ->add('clicks', null, ['sortable' => false])
        ;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return BusinessOverviewReport::getExportFormats();
    }

    /**
     * Manage filter parameters
     *
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();
        $datePeriodParams = AdminHelper::getDataPeriodParameters();

        if (!isset($parameters['date'])) {
            $parameters = $this->datagridValues = array_merge(
                $parameters,
                [
                    'date' => [
                        'value' => $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH],
                    ]
                ]
            );
        } else {
            $parameters['date']['value'] = $this->getValidDateRange(
                $parameters['date'],
                $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH]
            );
        }

        if (!isset($parameters['businessProfile'])) {
            $businessProfiles = $this->getBusinessProfilesForFilter();
            reset($businessProfiles);

            $parameters['businessProfile'] = [
                'type' => '',
                'value' => !empty($businessProfiles) ? key($businessProfiles) : 0,
            ];
        }

        return $parameters;
    }

    protected function getBusinessProfilesForFilter()
    {
        return $this->getDoctrine()->getRepository(BusinessProfile::class)
            ->getBusinessProfilesForFilter();
    }

    protected function getDoctrine()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine');
    }
}
