<?php

namespace Domain\BannerBundle\Admin;

use Domain\BannerBundle\Entity\Banner;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BannerAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('isPublished')
            ->add('updatedAt', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
            ->add('updatedUser')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('title')
            ->add('description')
            ->add('size')
            ->add('isPublished')
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
            ->with('General', ['class' => 'col-md-6',])->end()
            ->with('Comments', ['class' => 'col-md-6',])->end()
            ->with('Data', ['class' => 'col-md-6',])->end()
            ->end()
        ;

        $formMapper
            ->with('General')
                ->add('title')
                ->add('description', null, [
                    'attr' => [
                        'class' => 'vertical-resize',
                    ],
                ])
            ->end()
            ->with('Comments')
                ->add('placement', null, [
                    'disabled' => true,
                ])
                ->add('comment', null, [
                    'disabled' => true,
                    'attr' => [
                        'class' => 'vertical-resize',
                    ],
                ])
            ->end()
            ->with('Data')
                ->add('htmlId')
                ->add('slotId')
                ->add('isPublished')
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
            ->add('size')
            ->add('placement')
            ->add('comment')
            ->add('htmlId')
            ->add('slotId')
            ->add('isPublished')
            ->add('updatedAt')
            ->add('updatedUser')
        ;
    }

    /**
     * @param string        $name
     * @param Banner|null   $banner
     *
     * @return bool
     */
    public function isGranted($name, $banner = null)
    {
        $deniedActions = $this->getDeniedAllButViewAndEditActions();

        if (in_array($name, $deniedActions)) {
            return false;
        }

        return parent::isGranted($name, $banner);
    }
}
