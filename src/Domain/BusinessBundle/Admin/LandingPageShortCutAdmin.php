<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\LandingPageShortCut;
use Domain\SearchBundle\Util\CacheUtil;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

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
                'template' => 'DomainBusinessBundle:Admin:LandingPageShortCut/list_locality.html.twig',
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
                ->add(
                    'searchItems',
                    CollectionType::class,
                    [
                        'by_reference'  => false,
                        'required'      => true,
                    ],
                    [
                        'edit'          => 'inline',
                        'delete_empty'  => false,
                        'inline'        => 'table',
                        'sortable'      => 'position',
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

    /**
     * @param string $name
     * @param string $template
     */
    public function setTemplate($name, $template)
    {
        $this->getTemplateRegistry()
            ->setTemplate('edit', 'DomainBusinessBundle:Admin:LandingPageShortCut/edit.html.twig');
    }

    /**
     * @param LandingPageShortCut $entity
     */
    public function postRemove($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postRemove($entity);
    }

    /**
     * @param LandingPageShortCut $entity
     */
    public function postPersist($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postPersist($entity);
    }

    /**
     * @param LandingPageShortCut $entity
     */
    public function postUpdate($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postUpdate($entity);
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('move', $this->getRouterIdParameter().'/move/{position}')
        ;
    }

    private function invalidateLandingShortcutCache(): void
    {
        $memcachedCache = $this->getConfigurationPool()->getContainer()->get('app.cache.memcached');
        CacheUtil::invalidateCacheByPrefix($memcachedCache, CacheUtil::PREFIX_HOMEPAGE_SHORTCUT . 'prefix');
    }
}
