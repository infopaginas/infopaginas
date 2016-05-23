<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class TaskAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $typeFieldOptions = [
            'choices'     =>  TaskType::getChoices(),
            'placeholder' => $this->trans('All'),
        ];

        $statusFieldOptions = [
            'choices'     =>  TaskStatusType::getChoices(),
            'placeholder' => $this->trans('All'),
        ];

        $datagridMapper
            ->add('type', 'doctrine_orm_choice', [], 'choice', $typeFieldOptions)
            ->add('status', 'doctrine_orm_choice', [], 'choice', $statusFieldOptions)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type', 'string', ['template' => 'DomainBusinessBundle:TaskAdmin:fields/type_field.html.twig',])
            ->add('modifiedAt', 'datetime', ['label' => $this->trans('Date'),])
            ->add('status', 'string', ['template' => 'DomainBusinessBundle:TaskAdmin:fields/status_field.html.twig'])
            ->add('reviewer', '', ['label' => $this->trans('Approved/Rejected By')])
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('status')
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
            ->add('type')
            ->add('status')
            ->add('rejectReason')
            ->add('createdAt')
            ->add('modifiedAt')
        ;
    }
}
