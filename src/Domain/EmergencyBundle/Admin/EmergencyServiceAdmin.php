<?php

namespace Domain\EmergencyBundle\Admin;

use Domain\EmergencyBundle\Entity\EmergencyArea;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class EmergencyServiceAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, [
                'show_filter' => true,
            ])
            ->add('useAsFilter')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('useAsFilter')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'disabled' => true,
            ])
            ->add('useAsFilter')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('useAsFilter')
        ;
    }

    /**
     * @param string        $action
     * @param EmergencyArea $area
     * @return bool
     */
    public function isGranted($action, $area = null)
    {
        $deniedActions = $this->getDeniedAllButViewAndEditActions();

        if (in_array($action, $deniedActions)) {
            return false;
        }

        return parent::isGranted($action, $area);
    }
}
