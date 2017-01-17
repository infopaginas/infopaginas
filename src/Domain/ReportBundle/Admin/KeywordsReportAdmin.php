<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 07.09.16
 * Time: 0:09
 */

namespace Domain\ReportBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\CoreBundle\Form\Type\EqualType;

/**
 * Class KeywordsReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class KeywordsReportAdmin extends ReportAdmin
{
    const KEYWORDS_PER_PAGE_COUNT = [5, 10, 15, 20, 25];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('businessProfile')
            ->remove('keywordsCount')
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
                    'attr' => [
                    ],
                ],
            ])
            ->add(
                'keywordsCount',
                'doctrine_orm_choice',
                [
                    'label' => 'Keywords Count'
                ],
                'choice',
                [
                    'choices' =>  self::KEYWORDS_PER_PAGE_COUNT,
                    'expanded' => false,
                    'mapped' => false,
                    'required' => true
                ]
            )
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->keywordsData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.keywords_report_manager')
            ->getKeywordsDataByFilterParams($filterParam);

        $this->keywords = array_values($this->keywordsData);

        $listMapper
            ->add('Keywords', null, ['sortable' => false])
            ->add('Number of Searches', null, ['sortable' => false])
        ;
    }

    /**
     * Manage filter parameters
     *
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        if (!isset($parameters['keywordsCount'])) {
            $parameters['keywordsCount'] = [
                'type' => '',
                'value' => 2,
            ];
        }

        if (!isset($parameters['businessProfile'])) {
            $parameters['businessProfile'] = [
                'type' => '',
                'value' => 1,
            ];
        }

        return $parameters;
    }

    protected function getDoctrine()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine');
    }
}