<?php

namespace Oxa\Sonata\MediaBundle\Admin;

use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Class MediaAdmin
 * @package Oxa\Sonata\MediaBundle\Admin
 */
class MediaAdmin extends BaseMediaAdmin
{
    public $showFilters = true;

    /**
     * @var bool
     */
    public $allowBatchRestore = true;

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->remove('enabled')
            ->remove('authorName')
            ->remove('cdnIsFlushable')
            ->remove('copyright')
            ->add('binaryContent', FileType::class, [
                'required' => false,
                'constraints' => new File(AdminHelper::getFormImageFileConstrain()),
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('url', UrlType::class, [
                'required' => false,
                'constraints' => new Url()
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $contexts = $this->getPool()->getContexts();
        $contextsChoices = [];

        foreach ($contexts as $name => $context) {
            $contextsChoices[$name] = $this->trans($name, [], 'SonataMediaBundle');
        }

        $datagridMapper->add(
            'context',
            ChoiceFilter::class,
            [
                'field_options' => [
                    'choices' => $contextsChoices,
                ],
                'field_type' => ChoiceType::class,
            ]
        );

        $datagridMapper->add(
            'providerName',
            ChoiceFilter::class,
            [
                'show_filter' => false,
            ]
        );

        $datagridMapper
            ->add('name')
            ->add('createdAt', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, ['template' => 'OxaSonataMediaBundle:MediaAdmin:list_image.html.twig'])
            ->add('createdAt')
        ;

        $parentCode = $this->getRequest()->get('pcode');
        $mediaCode = $this->getConfigurationPool()
            ->getContainer()
            ->get('oxa_sonata_media.admin.media')
            ->getCode();

        $this->showOtherContexts = true;

        if ($parentCode && $parentCode != $mediaCode) {
            $this->showOtherContexts = false;
        }

        $this->addGridActions($listMapper);
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->remove('export')
            ->remove('show')
            ->add('restore')
        ;
    }
}
