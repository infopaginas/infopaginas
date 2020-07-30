<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfileMediaUrl;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class BusinessProfileMediaUrlAdmin extends OxaAdmin
{
    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = [
        '_page'     => 1,
        '_per_page' => 25,
        '_sort_by'  => 'position',
    ];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('url')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('url')
            ->add('type')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', ChoiceType::class, [
                'choices'  => BusinessProfileMediaUrl::getAvailableTypes(),
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'translation_domain' => 'messages',
            ])
            ->add('url', UrlType::class, [
                'attr' => [
                    'required' => true,
                ],
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'hidden' => true,
                ]
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
            ->add('url')
            ->add('type')
        ;
    }
}
