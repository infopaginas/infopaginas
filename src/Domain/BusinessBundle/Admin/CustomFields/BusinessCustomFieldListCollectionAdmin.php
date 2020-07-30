<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldList;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessCustomFieldListCollectionAdmin extends OxaAdmin
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
            ->add('lists', ModelListType::class, [
                'required'      => true,
                'btn_add'       => false,
                'btn_delete'    => false,
                'model_manager' => $this->modelManager,
                'class'         => BusinessCustomFieldList::class,
            ])
            ->add('value', null, [
                'required' => true,
                'attr'     => ['class' => 'hidden list-value'],
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
            ->add('radioButtons')
            ->add('value')
        ;
    }
}
