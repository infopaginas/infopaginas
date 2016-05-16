<?php
namespace Oxa\Sonata\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class OxaAdmin extends BaseAdmin
{
    /**
     * Basic admin configuration
     */
    public function configure()
    {
        $this->perPageOptions = [10, 25, 50, 100, 250, 500];
        // custom delete page template
        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:delete.html.twig');
    }

    /**
     * Add additional actions
     *
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        // delete from database action
        if ($this->isGranted('ROLE_PHYSICAL_DELETE_ABLE') && $this->hasRoute('delete_physical')) {
            $actions['delete_physical'] = [
                'label' => $this->trans('action_delete_physical'),
                'ask_confirmation' => true
            ];
        }

        // restore deleted record
        if ($this->isGranted('ROLE_RESTORE_ABLE') && $this->hasRoute('restore')) {
            $actions['restore'] = [
                'label' => $this->trans('action_restore'),
                'ask_confirmation' => false
            ];
        }

        return $actions;
    }

    /**
     * Configure record list
     *
     * @return \Sonata\AdminBundle\Datagrid\DatagridInterface
     */
    public function getDatagrid()
    {
        // Display deleted records as well in the list
        if ($this->isGranted('ROLE_PHYSICAL_DELETE_ABLE')) {
            /* @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $softDeleteableFilter */
            $softDeleteableFilter = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.default_entity_manager')
                ->getFilters()
                ->getFilter('softdeleteable');

            $softDeleteableFilter->disableForEntity($this->getClass());
        }

        return parent::getDatagrid();
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('show')
            ->add('delete_physical', null, [
                '_controller' => 'OxaSonataAdminBundle:CRUD:deletePhysical'
            ])
            ->add('restore')
            ->add('copy');
    }

    /**
     * Show all available actions for a record
     *
     * @param ListMapper $listMapper
     */
    protected function addGridActions(ListMapper $listMapper)
    {
        $listMapper->add('_action', 'actions', [
            'actions' => [
                'all_available' => [
                    'template' => 'OxaSonataAdminBundle:CRUD:list__action_delete_physical_able.html.twig'
                ]
            ]
        ]);
    }
}
