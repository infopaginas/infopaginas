<?php

namespace Domain\ReportBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * Class ExportReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class ExportReportAdmin extends OxaAdmin
{
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    );

    protected $maxPerPage = 25;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type')
            ->add(
                'user',
                null,
                [
                    'label' => 'Requested by',
                ]
            )
            ->add('createdAt')
            ->add('status')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('type')
            ->add(
                'user',
                null,
                [
                    'label' => 'Requested by',
                ]
            )
            ->add('createdAt')
            ->add('status')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('type')
            ->add(
                'user',
                null,
                [
                    'label' => 'Requested by',
                ]
            )
            ->add('createdAt')
            ->add('status')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('type')
            ->add(
                'user',
                null,
                [
                    'label' => 'Requested by',
                ]
            )
            ->add('createdAt')
            ->add('status')
            ->add('params', null, [
                'template' => 'OxaSonataAdminBundle:ShowFields:show_json_array.html.twig',
            ])
            ->add('links', null, [
                'template' => 'DomainReportBundle:Admin:ExportReport/show_links.html.twig',
            ])
        ;
    }

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        $container = $this->getConfigurationPool()->getContainer();
        $checker   = $container->get('security.authorization_checker');

        if (!$checker->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $container->get('security.token_storage')->getToken()->getUser();
            $parameters['user'] = [
                'type'  => '',
                'value' => $user->getId(),
            ];
        }

        return $parameters;
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('edit')
            ->remove('create')
            ->remove('delete')
            ->remove('export')
            ->add('show')
        ;
    }

    public function getExportFormats()
    {
        return [
            'business_profile.admin.export.csv' => 'csv',
        ];
    }

    public function getExportFields()
    {
        $exportFields['ID']         = 'id';
        $exportFields['Type']       = 'type';
        $exportFields['UserName']   = 'user.fullName';
        $exportFields['UserId']     = 'user.id';
        $exportFields['CreatedAt']  = 'createdAt';
        $exportFields['Status']     = 'status';

        return $exportFields;
    }
}
