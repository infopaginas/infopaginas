<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BusinessProfileExtraSearchAdmin extends OxaAdmin
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
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('categories', null, [
                'multiple' => true,
                'required' => true,
                'query_builder' => function (\Domain\BusinessBundle\Repository\CategoryRepository $rep) {
                    return $rep->getAvailableCategoriesQb();
                },
            ])
            ->add('localities', null, [
                'multiple' => true,
                'required' => false,
                'label' => 'Localities',
                'query_builder' => function (\Domain\BusinessBundle\Repository\LocalityRepository $rep) {
                    return $rep->getAvailableLocalitiesQb();
                },
            ])
            ->add('serviceAreasType', ChoiceType::class, [
                'choices'  => BusinessProfileExtraSearch::getServiceAreasTypes(),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('milesOfMyBusiness', null, [
                'required' => false,
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('categories')
            ->add('localities')
            ->add('serviceAreasType')
            ->add('milesOfMyBusiness')
        ;
    }
}
