<?php

namespace Oxa\ConfigBundle\Admin;

use Domain\SearchBundle\Util\CacheUtil;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ConfigAdmin
 * @package Oxa\ConfigBundle\Admin
 */
class ConfigAdmin extends OxaAdmin
{
    protected $datagridValues = array(
        '_per_page' => 50,
        '_sort_by'  => 'title',
    );

    /**
     * @param Config $entity
     */
    public function postPersist($entity)
    {
        $this->removeConfigCache();
    }

    /**
     * @param Config $entity
     */
    public function postUpdate($entity)
    {
        $this->removeConfigCache();
        parent::postUpdate($entity);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('value')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title')
            ->add('value', null, array('template' => 'OxaConfigBundle:Admin:list__value.html.twig'))
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('description', null, [
                'attr' => [
                    'class' => 'vertical-resize',
                ],
            ])
            ->add('format', 'choice', [
                'choices' => [
                    'html' => 'html',
                    'text' => 'text'
                ],
                'attr' => [
                    'class' => 'formatter'
                ]
            ])
            ->add('value', 'ckeditor', [
                'config_name' => 'default',
                'attr' => [
                    'class' => 'vertical-resize',
                ],
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
            ->add('title')
            ->add('format')
            ->add('description')
            ->add('value', null, array('template' => 'OxaConfigBundle:Admin:show__value.html.twig'))
        ;
    }

    /**
     * @param string $context
     *
     * @return ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.isActive', ':isActive')
        );
        $query->setParameter('isActive', true);

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('export');
    }

    private function removeConfigCache()
    {
        $memcached = $this->getConfigurationPool()->getContainer()->get('app.cache.memcached');
        $memcached->delete(CacheUtil::ID_CONFIGS);
    }
}
