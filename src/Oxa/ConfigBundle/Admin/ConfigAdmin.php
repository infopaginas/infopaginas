<?php

namespace Oxa\ConfigBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ConfigAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('format')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title')
            ->add('format')
            ->add('value', null, array('template' => 'OxaConfigBundle:Admin:list__value.html.twig'))
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('key')
            ->add('format', 'choice', [
                'choices' => [
                    'html'=>'html',
                    'text'=>'text'
                ],
                'attr' => [
                    'class' => 'formatter'
                ]
            ])
            ->add('value', 'ckeditor', ['config_name' => 'default'])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('key')
            ->add('title')
            ->add('value')
            ->add('format')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
//        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('export');
    }
}
