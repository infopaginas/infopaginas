<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\UserActionReport;
use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\UserActionModel;
use Oxa\Sonata\AdminBundle\Filter\CaseInsensitiveStringFilter;
use Oxa\Sonata\AdminBundle\Filter\DateRangeFilter;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\CoreBundle\Form\Type\DateRangePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class UserActionReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class UserActionReportAdmin extends ReportAdmin
{
    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * Basic admin configuration
     */
    public function configure()
    {
        parent::configure();

        $this->setPerPageOptions(
            [
                5,
                10,
                15,
                20,
                25,
                50,
                100,
                500,
            ]
        );
    }

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'     => 1,
        '_per_page' => 15,
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username', ModelFilter::class, [
                'show_filter' => !empty($this->datagridValues['username']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'property'  => 'fullName',
                    'class' => 'Oxa\Sonata\UserBundle\Entity\User',
                    'query_builder' => function (\Oxa\Sonata\UserBundle\Entity\Repository\UserRepository $rep) {
                        return $rep->findByRolesQb(
                            [
                                'ROLE_SALES_MANAGER',
                                'ROLE_CONTENT_MANAGER',
                                'ROLE_ADMINISTRATOR',
                            ]
                        );
                    },
                ],
            ])
            ->add('entity', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['entity']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('entityName', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['entityName']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('action', ChoiceFilter::class, [
                'show_filter' => !empty($this->datagridValues['action']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => UserActionModel::EVENT_TYPES,
                    'choice_translation_domain' => 'AdminReportBundle',
                ],
                'field_type' => ChoiceType::class
            ])
            ->add('date', DateRangeFilter::class, [
                'show_filter' => $this->checkDateFilter() ?: null,
                'field_type' => DateRangePickerType::class,
                'field_options' => [
                    'field_options' => [
                        'format'        => AdminHelper::FILTER_DATE_RANGE_FORMAT,
                        'empty_value'   => false,
                    ],
                    'mapped'    => false,
                    'required'  => true,
                ],
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->userActions = $this->getUserActionReportManager()->getUserActionReportData($filterParam);
        $this->events = UserActionModel::EVENT_TYPES;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return UserActionReport::getExportFormats();
    }

    /**
     * @return UserActionReportManager
     */
    protected function getUserActionReportManager() : UserActionReportManager
    {
        return $this->getConfigurationPool()->getContainer()->get('domain_report.manager.user_action_report_manager');
    }

    /**
     * @return bool
     */
    protected function checkDateFilter()
    {
        if (!empty($this->datagridValues['date']['value']['start']) or
            !empty($this->datagridValues['date']['value']['end'])
        ) {
            return true;
        } else {
            return false;
        }
    }
}
