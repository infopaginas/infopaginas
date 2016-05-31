<?php

namespace Domain\BannerBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class BannerAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $choiceOptions = [
            'choices' => [
                1 => 'label_yes',
                2 => 'label_no',
            ],
            'translation_domain' => $this->getTranslationDomain()
        ];

        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('allowedForBusinesses', null, [], null, $choiceOptions)
            ->add('isActive', null, [], null, $choiceOptions)
            ->add('type')
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
            ->add('image', null, ['template' => 'DomainBannerBundle:Admin:list_image.html.twig'])
            ->add('title')
            ->add('type')
            ->add('size')
            ->add('allowedForBusinesses')
            ->add('isActive')
            ->add('sorting', null, ['template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'])
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
            ->with('Type', array('class' => 'col-md-6'))->end()
            ->with('Template', array('class' => 'col-md-6'))->end()
            ->end()
        ;

        $formMapper
            ->with('General')
            ->add('title')
            ->add('description')
            ->add('allowedForBusinesses')
            ->add('isActive')
            ->end()
            ->with('Type')
            ->add('type', 'sonata_type_model_list', [], [])
            ->add('image', 'sonata_type_model_list', [], array(
                'link_parameters' => array(
                    'context' => OxaMediaInterface::CONTEXT_BANNER,
                    'provider' => OxaMediaInterface::PROVIDER_IMAGE,
            )))
            ->end()
            ->with('Template')
            ->add('template', 'sonata_type_model_list', [], [
                'required' => false
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
            ->add('title')
            ->add('description')
            ->add('type')
            ->add('size')
            ->add('allowedForBusinesses')
            ->add('isActive')
            ->add('image', null, [
                'template' => 'DomainBannerBundle:Admin:show_image.html.twig'
            ])
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('type')
            ->assertNotBlank()
            ->assertNotNull()
            ->end()
            ->with('image')
            ->assertNotBlank()
            ->assertNotNull()
            ->end()
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('copy');
    }
}
