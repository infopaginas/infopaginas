<?php

namespace Domain\PageBundle\Admin;

use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Model\PageInterface;
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
            ->add('name', null, [
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
            ->addIdentifier('name')
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
        /* @var Page $page*/
        $page       = $this->getSubject();
        $pageCode   = $page->getCode();

        if (!$pageCode) {
            $pageCode = PageInterface::CODE_DEFAULT;
        }

        $helpMessage = Page::getPageSeoHintByCode($pageCode);

        // define group zoning
        $formMapper
            ->with('General', ['class' => 'col-md-6'])->end()
            ->with('Status', ['class' => 'col-md-6'])->end()
        ;

        if (in_array($pageCode, Page::getStaticPage())) {
            $formMapper->with('Body')->end();
        }

        $formMapper->with('Seo')->end();

        $formMapper
            ->with('General')
                ->add('title', null, [
                    'help' => $this->getHelpMessage('title', $helpMessage),
                ])
                ->add('description', null, [
                    'help' => $this->getHelpMessage('description', $helpMessage),
                ])
            ->end()
        ;

        if (in_array($pageCode, Page::getStaticPage())) {
            $formMapper
                ->with('General')
                    ->add('redirectUrl')
                ->end()
            ;
        }

        if ($pageCode == PageInterface::CODE_LANDING) {
            $formMapper
                ->with('General')
                    ->add(
                        'background',
                        'sonata_type_model_list',
                        [
                            'required' => false,
                        ],
                        [
                            'link_parameters' => [
                                'context'  => OxaMediaInterface::CONTEXT_PAGE_BACKGROUND,
                                'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                            ],
                        ]
                    )
                ->end()
            ;
        }

        $formMapper
            ->with('Status')
                ->add('name', null, [
                    'required' => false,
                    'disabled' => true,
                ])
                ->add('updatedAt', 'sonata_type_datetime_picker', [
                    'required' => false,
                    'disabled' => true,
                ])
                ->add('updatedUser', TextType::class, [
                    'required' => false,
                    'disabled' => true,
                ])
            ->end()
        ;

        if (in_array($pageCode, Page::getStaticPage())) {
            $formMapper
                ->with('Status')
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
                    ->add('slug', null, [
                        'read_only' => true,
                        'required'  => false,
                    ])
                ->end()
            ;

            $formMapper
                ->with('Body')
                    ->add('body', 'ckeditor')
                ->end()
            ;
        }

        $formMapper
            ->with('Seo')
                ->add('seoTitle', null, [
                    'help' => $this->getHelpMessage('seoTitle', $helpMessage),
                ])
                ->add('seoDescription', null, [
                    'help' => $this->getHelpMessage('seoDescription', $helpMessage),
                ])
            ->end()
        ;

        if ($pageCode == PageInterface::CODE_EMERGENCY) {
            $formMapper
                ->with('Content')
                    ->add('contentUpdatedAt', 'sonata_type_datetime_picker', [
                        'required' => false,
                        'disabled' => true,
                    ])
                    ->add('actionLink', null, [
                        'help' => $this->getHelpMessage('actionLink', $helpMessage),
                    ])
                    ->add(
                        'links',
                        'sonata_type_collection',
                        [
                            'by_reference'  => false,
                            'required'      => false,
                        ],
                        [
                            'edit'          => 'inline',
                            'delete_empty'  => false,
                            'inline'        => 'table',
                        ]
                    )
                ->end()
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /* @var Page $page*/
        $page       = $this->getSubject();
        $pageCode   = $page->getCode();

        $showMapper
            ->add('id')
            ->add('name')
            ->add('title')
            ->add('description')
        ;

        if (in_array($pageCode, Page::getStaticPage())) {
            $showMapper
                ->add('body', null, [
                    'template' => 'DomainPageBundle:Admin:show__body.html.twig',
                ])
                ->add('slug')
            ;
        }

        if ($pageCode == PageInterface::CODE_LANDING) {
            $showMapper
                ->add('background', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_many_to_one.html.twig',
                ])
            ;
        }

        $showMapper
            ->add('seoTitle')
            ->add('seoDescription')
        ;

        $showMapper
            ->add('updatedAt')
            ->add('updatedUser')
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

    /**
     * @param string $field
     * @param array  $helpMessage
     *
     * @return string
     */
    protected function getHelpMessage($field, $helpMessage)
    {
        return $this->trans(
            $helpMessage[$field],
            [
                '{placeholders}' => implode(', ', $helpMessage['placeholders'])
            ]
        );
    }
}
