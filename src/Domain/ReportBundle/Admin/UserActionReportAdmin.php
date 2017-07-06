<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\UserActionReport;
use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\UserActionModel;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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

    protected function getUserActionReportManager() : UserActionReportManager
    {
        return $this->getConfigurationPool()->getContainer()->get('domain_report.manager.user_action_report_manager');
    }
}
