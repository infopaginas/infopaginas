<?php
namespace Application\Sonata\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin as BaseAdminAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ApplicationAdmin extends BaseAdminAdmin
{
    public function configure()
    {
        $this->perPageOptions = [10, 25, 50, 100, 250, 500];
        $this->setTemplate('delete', 'ApplicationSonataAdminBundle:CRUD:delete.html.twig');
    }

    public function getBatchActions()
    {
        $actions =  parent::getBatchActions();

        if ($this->hasRoute('delete')) {
            $actions['delete']['ask_confirmation'] = false;
        }

        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        if ($authorizationChecker->isGranted('ROLE_PHYSICAL_DELETE_ABLE') && $this->hasRoute('delete_physical')) {
            $actions['delete_physical'] = [
                'label'            => $this->trans('action_delete_physical'),
                'ask_confirmation' => true
            ];
        }

        if ($this->hasRoute('copy')) {
            $actions['copy'] = [
                'label'            => $this->trans('action_copy'),
                'ask_confirmation' => false
            ];
        }

        if ($authorizationChecker->isGranted('ROLE_RESTORE_ABLE') && $this->hasRoute('restore')) {
            $actions['restore'] = [
                'label'            => $this->trans('action_restore'),
                'ask_confirmation' => false
            ];
        }

        return $actions;
    }

    public function getDatagrid()
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->get('security.authorization_checker')->isGranted('ROLE_PHYSICAL_DELETE_ABLE')) {
            /* @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $softDeleteableFilter */
            $softDeleteableFilter = $container
                ->get('doctrine.orm.default_entity_manager')
                ->getFilters()
                ->getFilter('softdeleteable');

            $softDeleteableFilter->disableForEntity($this->getClass());
        }

        return parent::getDatagrid();
    }

    public function getCULabel()
    {
        return $this->trans('Section');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('delete_physical', null, [
                '_controller' => 'ApplicationSonataAdminBundle:DefaultEntityCRUD:deletePhysical'
            ])
            ->add('restore')
            ->add('copy')
//            ->add('show')
        ;
    }

    protected function addGridActions(ListMapper $listMapper)
    {
        $listMapper->add('_action', 'actions', [
            'actions' => [
                'all_available' => ['template' => 'ApplicationSonataAdminBundle:CRUD:list__action_delete_physical_able.html.twig']
            ]
        ]);
    }
}
