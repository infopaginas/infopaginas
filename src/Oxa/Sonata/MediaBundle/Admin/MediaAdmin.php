<?php

namespace Oxa\Sonata\MediaBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as SonataMediaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class MediaAdmin
 * @package Oxa\Sonata\MediaBundle\Admin
 */
class MediaAdmin extends BaseMediaAdmin
{
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
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('providerReference')
        ;

        $context = $this->getPersistentParameter('context');
        $datagridMapper->add('context', 'doctrine_orm_choice', ['field_options' => ['choices' => [
            $context => $this->trans($context, [], 'SonataMediaBundle')
        ]],
            'field_type' => 'choice',
        ]);

        $provider = $this->getPersistentParameter('provider');
        $datagridMapper->add('providerName', 'doctrine_orm_choice', ['field_options' => ['choices' => [
            $provider => $this->trans($provider, [], 'SonataMediaBundle')
        ]],
            'field_type' => 'choice',
        ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, ['template' => 'OxaSonataMediaBundle:MediaAdmin:list_image.html.twig'])
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
        ;
    }
}
