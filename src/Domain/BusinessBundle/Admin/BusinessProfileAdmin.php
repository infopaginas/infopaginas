<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessProfileAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('website')
            ->add('email')
            ->add('phone')
            ->add('registrationDate')
            ->add('slogan')
            ->add('description')
            ->add('product')
            ->add('workingHours')
            ->add('isSetDescription')
            ->add('isSetMap')
            ->add('isSetAd')
            ->add('isSetLogo')
            ->add('isSetSlogan')
            ->add('slug')
            ->add('updatedAt')
            ->add('isActive')
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
            ->add('user')
            ->add('subscription')
            ->add('categories')
            ->add('email')
            ->add('phone')
            ->add('slug')
            ->add('updatedAt')
            ->add('isActive')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('Profile', array('class' => 'col-md-6'))
                ->with('General', array('class' => 'col-md-4'))->end()
                ->with('Description', array('class' => 'col-md-8'))->end()
                ->with('Categories', array('class' => 'col-md-12'))->end()
            ->end()
            ->tab('Status', array('class' => 'col-md-6'))
                ->with('General', array('class' => 'col-md-6'))->end()
                ->with('Displayed blocks', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Address', array('class' => 'col-md-6'))
            ->end()
            ->tab('Media', array('class' => 'col-md-6'))
            ->end()
            ->tab('SEO', array('class' => 'col-md-6'))
            ->end()
        ;

        $formMapper
            ->tab('Profile')
                ->with('General')
                    ->add('name')
                    ->add('user', 'sonata_type_model_list', [
                        'required' => true,
                        'btn_delete' => false,
                        'btn_add' => false,
                    ])
                    ->add('website')
                    ->add('email', 'email')
                    ->add('phone')
                    ->add('workingHours')
                    ->add('slug', null, [
                        'read_only' => true,
                        'required' => false,
                    ])
                ->end()
                ->with('Description')
                    ->add('slogan')
                    ->add('product')
                    ->add('description', 'ckeditor')
                ->end()
                ->with('Categories')
//                    ->add('areas', 'sonata_type_model_list', [
//                        'allow_extra_fields' => false,
////                        'compound' => true,
////                        'by_reference' => true,
////                        'multiple' => true,
////                        'allow_add' => true
//                    ], array(
//                        'placeholder' => 'Nothing selected',
//                        'expanded' => true, 'multiple' => true, 'by_reference' => false
//                    ))
                    ->add('areas', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('brands', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('tags', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('categories', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])

                    ->add('paymentMethods', null, [
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('Status')
                ->with('General')
                    ->add('subscription', null, [])
                    ->add('isActive')
                ->end()
                ->with('Displayed blocks')
                    ->add('isSetDescription')
                    ->add('isSetMap')
                    ->add('isSetAd')
                    ->add('isSetLogo')
                    ->add('isSetSlogan')
                ->end()
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
            ->add('name')
            ->add('website')
            ->add('email')
            ->add('phone')
            ->add('registrationDate')
            ->add('slogan')
            ->add('description')
            ->add('product')
            ->add('workingHours')
            ->add('isSetDescription')
            ->add('isSetMap')
            ->add('isSetAd')
            ->add('isSetLogo')
            ->add('isSetSlogan')
            ->add('slug')
            ->add('deletedAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('isActive')
        ;
    }
}
