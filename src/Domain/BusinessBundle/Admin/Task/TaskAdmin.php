<?php

namespace Domain\BusinessBundle\Admin\Task;

use Domain\BusinessBundle\Entity\Task\Task;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class TaskAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type', 'doctrine_orm_choice', [
                'field_options' => [
                    'required' => false,
                    'choices' => Task::getTypes()
                ],
                'field_type' => 'choice'
            ])
            ->add('status', 'doctrine_orm_choice', [
                'field_options' => [
                    'required' => false,
                    'choices' => Task::getStatuses()
                ],
                'field_type' => 'choice'
            ])
            ->add('reviewer')
            ->add('businessProfile')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('typeName')
            ->add('statusName')
            ->add('reviewer')
            ->add('businessProfile')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type')
            ->add('status')
            ->add('reviewer')
            ->add('businessProfile')
            ->add('rejectReason')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('typeName')
            ->add('statusName')
            ->add('reviewer')
            ->add('businessProfile')
            ->add('rejectReason')
        ;
    }

}
