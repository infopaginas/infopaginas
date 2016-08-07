<?php

namespace Domain\PageBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PageAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('isPublished', null, [], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ],
                'translation_domain' => $this->getTranslationDomain()
            ])
            ->add('template')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('image', null, ['template' => 'DomainPageBundle:Admin:list_image.html.twig'])
            ->add('title')
            ->add('template')
            ->add('isPublished', null, ['editable' => true])
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
            ->with('General', array('class' => 'col-md-6'))->end()
            ->with('Status', array('class' => 'col-md-6'))->end()
            ->with('Body', array('class' => 'col-md-12'))->end()
        ;

        $formMapper
            ->with('General')
                ->add('title')
                ->add('description')
                ->add(
                    'image',
                    'sonata_type_model_list',
                    [
                        'required' => false,
                    ],
                    [
                        'link_parameters' => [
                            'context' => OxaMediaInterface::CONTEXT_PAGE,
                            'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        ]
                    ]
                )
                ->add(
                    'template',
                    'sonata_type_model_list',
                    [
                        'required' => false,
                        'btn_add' => false,
                    ]
                )
            ->end()
            ->with('Status')
                ->add('isPublished')
                ->add('updatedAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('updatedUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
                ->add('slug', null, ['read_only' => true, 'required' => false])
            ->end()
            ->with('Body')
                ->add('body', 'ckeditor')
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
            ->add('title')
            ->add('image', null, [
                'template' => 'DomainPageBundle:Admin:show_image.html.twig'
            ])
            ->add('description')
            ->add('body', null, array('template' => 'DomainPageBundle:Admin:show__body.html.twig'))
            ->add('isPublished')
            ->add('slug')
        ;
    }
}
