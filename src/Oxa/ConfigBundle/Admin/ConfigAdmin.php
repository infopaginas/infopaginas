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
//            ->add('sorting', null, ['template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'])
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
            ->add('format', 'choice', array(
                'choices' => array(
                    'html'=>'html',
                    'text'=>'text'
                ),
                'attr' => array(
                    'class' => 'formatter'
                )
            ))
            ->add('value', 'ckeditor', array('config_name' => 'default'))
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
