<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class LandingPageShortCutSearchAdmin extends OxaAdmin
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
            ->add('titleEn')
            ->add('titleEs')
            ->add('searchTextEn')
            ->add('searchTextEs')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Locality', ['class' => 'col-md-6',])
                ->add('titleEn', null, [
                    'label' => 'Search For English',
                ])
                ->add('searchTextEn', null, [
                    'label' => 'Title English',
                ])
            ->end()
            ->with('Spanish', ['class' => 'col-md-6',])
                ->add('titleEs', null, [
                    'label' => 'Title Spanish',
                ])
                ->add('searchTextEs', null, [
                    'label' => 'Search For Spanish',
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
            ->add('titleEn')
            ->add('titleEs')
            ->add('searchTextEn')
            ->add('searchTextEs')
        ;
    }
}
