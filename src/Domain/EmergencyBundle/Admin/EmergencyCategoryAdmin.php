<?php

namespace Domain\EmergencyBundle\Admin;

use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class EmergencyCategoryAdmin extends OxaAdmin
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
            ->add('position')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('position')
            ->add('slug', null, [
                'read_only' => true,
                'required'  => false
            ])
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
            ->add('position')
            ->add('slug')
        ;
    }

    /**
     * @param string            $action
     * @param EmergencyCategory $category
     * @return bool
     */
    public function isGranted($action, $category = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if (in_array($action, $deniedActions)) {
            return false;
        }

        return parent::isGranted($action, $category);
    }
}
