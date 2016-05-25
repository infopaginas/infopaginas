<?php

namespace Oxa\Sonata\MediaBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as  SonataMediaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MediaAdmin extends SonataMediaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('providerReference')
        ;

        $provider = $this->getPersistentParameter('provider');
        $datagridMapper->add('providerName', 'doctrine_orm_choice', array(
            'field_options' => array(
                'choices'  => [$provider => $this->trans($provider, [], 'SonataMediaBundle')],
            ),
            'field_type' => 'choice',
        ));

        $context = $this->getPersistentParameter('context');
        $datagridMapper->add('context', 'doctrine_orm_choice', array(
            'field_options' => array(
                'choices'  => [$context => $this->trans($context, [], 'SonataMediaBundle')],
            ),
            'field_type' => 'choice',
        ));
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, ['template' => 'OxaSonataMediaBundle:MediaAdmin:list_image.html.twig'])
        ;
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        return [];
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->remove('export')
        ;
    }
}
