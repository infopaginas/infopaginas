<?php

namespace Domain\BusinessBundle\Admin\Media;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessProfileHasMediaAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('media.name')
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
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfileHasMedia/list_image.html.twig'
            ])
            ->add('media.name')
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
            ->add('media', 'sonata_type_model_list', array(
                'required' => false,
//                'btn_delete' => false,
            ), array(
                'link_parameters' => array(
                    'context' => 'business_profile_images'
                )
            ))
        ;

        // show this field if we edit this page on this page itself
        // (not through relation from other admin page)
        if ($this->getRoot()->getClass() == $this->getClass()) {
            $formMapper->add('businessProfile', null, [
                'required' => true,
            ]);
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfileHasMedia/show_image.html.twig'
            ])
            ->add('media.name')
            ->add('businessProfile')
        ;
    }
}
