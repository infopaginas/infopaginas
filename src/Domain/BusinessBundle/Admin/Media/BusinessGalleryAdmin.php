<?php

namespace Domain\BusinessBundle\Admin\Media;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessGalleryAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $boolChoiceOptions = [
            'choices' => [
                1 => 'label_yes',
                2 => 'label_no',
            ],
            'translation_domain' => 'AdminDomainBusinessBundle'
        ];

        $datagridMapper
            ->add('id')
            ->add('media.name')
            ->add('businessProfile')
            ->add('isPrimary', null, [], null, $boolChoiceOptions)
            ->add('isActive', null, [], null, $boolChoiceOptions)
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
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/list_image.html.twig'
            ])
            ->add('media.name')
            ->addIdentifier('description')
            ->add('businessProfile')
            ->add('isPrimary', null, ['editable' => true])
            ->add('isActive', null, ['editable' => true])
            ->add('sorting', null, ['template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'])
        ;
        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // show this field if we edit this page on this page itself
        // (not through relation from other admin page)
        if ($this->getRoot()->getClass() == $this->getClass()) {
            $formMapper->add('businessProfile', null, [
                'required' => true,
            ]);
        }

        $formMapper
            ->add('media', 'sonata_type_model_list', array(
                'required' => false,
//                'btn_delete' => false,
            ), array(
                'link_parameters' => array(
                    'context' => 'business_profile_images',
                    'provider' => 'sonata.media.provider.image',
                    'allow_switch_context' => false
                )
            ))
            ->add('description', null, [
                'attr' => [
                    'rows' => 2,
                    'cols' => 100,
                    'style' => 'resize: none'
                ]
            ])
            ->add('isPrimary')
            ->add('isActive')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/show_image.html.twig'
            ])
            ->add('media.name')
            ->add('businessProfile')
            ->add('description')
            ->add('isPrimary')
            ->add('isActive')
        ;
    }
}
