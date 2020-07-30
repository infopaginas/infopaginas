<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckbox;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessCustomFieldCheckboxCollectionAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'checkboxes',
                ModelListType::class,
                [
                    'required'      => true,
                    'btn_add'       => false,
                    'btn_delete'    => false,
                    'model_manager' => $this->modelManager,
                    'class'         => BusinessCustomFieldCheckbox::class,
                ]
            )
            ->add('isAvailable')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('checkboxes')
            ->add('isAvailable')
        ;
    }
}
