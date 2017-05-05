<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class LandingPageShortCutAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('locality')
            ->add('useAllLocation')
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
            ->add('locality', null, [
                'template' => 'DomainBusinessBundle:Admin:LandingPageShortCut/list_locality.html.twig'
            ])
            ->add('useAllLocation')
            ->add('isActive')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Landing Page Short Cut', ['class' => 'col-md-12',])
                ->with('Locality', ['class' => 'col-md-12',])->end()
                ->with('Searches', ['class' => 'col-md-12',])->end()
            ->end()
        ;

        $formMapper
            ->tab('Landing Page Short Cut')
                ->with('Locality')
                    ->add('locality', null, [
                        'required' => true,
                    ])
                    ->add('useAllLocation')
                    ->add('isActive')
                ->end()
                ->with('Searches')
                ->add('searchItems', 'sonata_type_collection',
                    [
                        'by_reference'  => false,
                        'required'      => true,
                    ],
                    [
                        'edit'          => 'inline',
                        'delete_empty'  => false,
                        'inline'        => 'table',
                    ]
                )
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
            ->add('locality')
            ->add('isActive')
            ->add('searchItems')
        ;
    }

    public function setTemplate($name, $template)
    {
        $this->templates['edit'] = 'DomainBusinessBundle:Admin:LandingPageShortCut/edit.html.twig';
    }
}
