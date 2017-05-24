<?php

namespace Domain\PageBundle\Admin;

use Domain\PageBundle\Entity\Page;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PageAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, [
                'show_filter' => true,
            ])
            ->add('updatedUser')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('updatedAt')
            ->add('updatedUser')
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
                ->add('background', 'sonata_type_model_list',
                    [
                        'required' => false,
                    ],
                    [
                        'link_parameters' => [
                            'context'  => OxaMediaInterface::CONTEXT_PAGE_BACKGROUND,
                            'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        ]
                    ])
            ->end()
            ->with('Status')
                ->add('updatedAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('updatedUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
                ->add('url', TextType::class, [
                    'mapped' => false,
                    'read_only' => true,
                    'required' => false,
                    'data' => sprintf(
                        '%s/%s',
                        $this->getRequest()->getHost(),
                        $this->getSubject()->getSlug()
                    )
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
            ->add('body', null, array('template' => 'DomainPageBundle:Admin:show__body.html.twig'))
            ->add('updatedAt')
            ->add('updatedUser')
            ->add('slug')
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->remove('delete')
            ->remove('remove')
            ->remove('create')
        ;
    }

    public function prePersist($entity)
    {
        $this->preSave($entity);
    }

    public function preUpdate($entity)
    {
        /** @var Page $entity */
        $this->preSave($entity);
    }

    private function preSave($entity)
    {
        $entity = $this->setSeoDate($entity);
    }

    /**
     * @param Page $entity
     *
     * @return Page
     */
    private function setSeoDate($entity)
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        $entity = $container->get('domain_page.manager.page')->setPageSeoData($entity, $container);

        return $entity;
    }
}
