<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\LandingPageShortCutSearch;
use Domain\SearchBundle\Util\CacheUtil;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class LandingPageShortCutSearchAdmin extends OxaAdmin
{
    /**
     * @param LandingPageShortCutSearch $entity
     */
    public function postRemove($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postRemove($entity);
    }

    /**
     * @param LandingPageShortCutSearch $entity
     */
    public function postPersist($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postPersist($entity);
    }

    /**
     * @param LandingPageShortCutSearch $entity
     */
    public function postUpdate($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postUpdate($entity);
    }

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
            ->with('Search', ['class' => 'col-md-6',])
                ->add('searchTextEn', null, [
                    'label' => 'Search For English',
                ])
                ->add('searchTextEs', null, [
                    'label' => 'Search For Spanish',
                ])
            ->end()
            ->with('Title', ['class' => 'col-md-6',])
                ->add('titleEn', null, [
                    'label' => 'Title English',
                ])
                ->add('titleEs', null, [
                    'label' => 'Title Spanish',
                ])
            ->end()
            ->add('position', 'hidden', [
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
            ->add('titleEn')
            ->add('titleEs')
            ->add('searchTextEn')
            ->add('searchTextEs')
        ;
    }

    private function invalidateLandingShortcutCache(): void
    {
        $memcachedCache = $this->getConfigurationPool()->getContainer()->get('app.cache.memcached');
        CacheUtil::invalidateCacheByPrefix($memcachedCache, CacheUtil::PREFIX_HOMEPAGE_SHORTCUT . 'prefix');
    }
}
