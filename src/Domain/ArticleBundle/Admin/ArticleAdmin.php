<?php

namespace Domain\ArticleBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ArticleAdmin extends OxaAdmin
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
            ->add('category')
            ->add('isPublished', null, [], null, $choiceOptions)
            ->add('isOnHomepage', null, [], null, $choiceOptions)
            ->add('activationDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
            ->add('expirationDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
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
            ->add('title')
            ->add('category')
            ->add('isPublished')
            ->add('isOnHomepage')
            ->add('activationDate')
            ->add('expirationDate')
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
            ->with('General', array('class' => 'col-md-4'))->end()
            ->with('Content', array('class' => 'col-md-8'))->end()
        ;

        $formMapper
            ->with('General')
                ->add('title')
                ->add('category')
                ->add('image', 'sonata_type_model_list', [], ['link_parameters' => [
                    'context' => OxaMediaInterface::CONTEXT_ARTICLE,
                    'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                ]])
                ->add('isPublished')
                ->add('isOnHomepage')
                ->add('slug', null, ['read_only' => true])
                ->add('activationDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
                ->add('expirationDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
                ->add('updatedAt', 'sonata_type_datetime_picker', [
                    'required' => false,
                    'disabled' => true
                ])
                ->add('updatedUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
            ->end()
            ->with('Content')
                ->add('body', 'ckeditor', [
                    'required' => true
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
            ->add('activationDate')
            ->add('expirationDate')
            ->add('category')
            ->add('image', null, [
                'template' => 'DomainArticleBundle:Admin:show_image.html.twig'
            ])
            ->add('body', null, [
                'template' => 'DomainArticleBundle:Admin:show_body.html.twig'
            ])
            ->add('isPublished')
            ->add('isOnHomepage')
            ->add('slug')
            ->add('updatedAt')
            ->add('updatedUser')
        ;
    }
}
