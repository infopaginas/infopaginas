<?php

namespace Domain\ArticleBundle\Admin\Media;

use Domain\ArticleBundle\Entity\Article;
use Domain\ArticleBundle\Entity\Media\ArticleGallery;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

class ArticleGalleryAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('media.name')
            ->add('article.title')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/list_image.html.twig',
            ])
            ->add('media.name')
            ->addIdentifier('description')
            ->add('article')
            ->add('sorting', null, ['template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'])
        ;
        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /**
         * @var $articleGallery ArticleGallery
         */
        $articleGallery = $this->getSubject();

        if ($articleGallery) {
            $isExternal = $articleGallery->getArticle()->getIsExternal();
        } else {
            $isExternal = false;
        }

        if ($isExternal) {
            $property = [
                'read_only'     => true,
                'btn_add'       => false,
                'btn_list'      => false,
                'btn_delete'    => false,
            ];
        } else {
            $property = [
                'read_only'     => false,
            ];
        }

        $formMapper
            ->add('media', 'sonata_type_model_list', $property,
                [
                    'link_parameters' => [
                        'required' => true,
                        'context' => OxaMediaInterface::CONTEXT_ARTICLE_IMAGES,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        'allow_switch_context' => false,
                    ]
                ])
            ->add('description', null, ['attr' => [
                'rows'          => 2,
                'cols'          => 100,
                'style'         => 'resize: none',
                'required'      => true,
                'placeholder'   => 'Create an image description as ' .
                    'if you were describing the image to someone who cannot see it',
            ]])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/show_image.html.twig',
            ])
            ->add('media.name')
            ->add('article')
            ->add('description')
        ;
    }
}