<?php

namespace Domain\BusinessBundle\Admin\Review;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessReviewAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('user')
            ->add('businessProfile')
            ->add('username')
            ->add('rate', 'doctrine_orm_choice', [
                'field_options' => [
                    'required' => false,
                    'choices' => range(0, 5),
                ],
                'field_type' => 'choice'
            ])
            ->add('isActive', null, [], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ],
                'translation_domain' => 'AdminDomainBusinessBundle'
            ])
            ->add('createdAt', 'doctrine_orm_datetime_range', array(
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => array('format' => 'dd-MM-y hh:mm:ss')
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user')
            ->add('businessProfile')
            ->add('username')
            ->add('rating')
            ->add('isActive')
            ->add('createdAt')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-4'))->end()
            ->with('Review', array('class' => 'col-md-8'))->end()
            ;

        $formMapper
            ->with('General')
                ->add('user')
                ->add('businessProfile', null, [
                    // hide this field if this page used as sonata_type_collection on other pages
                    'attr' => ['hidden' => $this->getRoot()->getClass() != $this->getClass() ]
                ])
                ->add('isActive')
            ->end()
            ->with('Review')
                ->add('username', null, [

                ])
                ->add('rating', 'choice', [
                    'choices' => range(0, 5),
                    'required' => false
                ])
                ->add('content', null, [
                    'attr' => [
                        'rows' => 3,
                        'cols' => 100,
                        'style' => 'resize: none'
                    ]
                ])
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('rating')
            ->add('content')
            ->add('isActive')
        ;
    }
}
