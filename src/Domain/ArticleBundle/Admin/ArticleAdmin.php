<?php

namespace Domain\ArticleBundle\Admin;

use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleAdmin extends OxaAdmin
{
    /**
     * @var bool
     */
    public $allowBatchRestore = true;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, [
                'show_filter' => true,
            ])
            ->add('category')
            ->add('isPublished')
            ->add('isOnHomepage')
            ->add('activationDate', DateTimeRangeFilter::class, $this->defaultDatagridDateTypeOptions)
            ->add('expirationDate', DateTimeRangeFilter::class, $this->defaultDatagridDateTypeOptions)
            ->add('updatedAt', DateTimeRangeFilter::class, $this->defaultDatagridDateTypeOptions)
            ->add('updatedUser')
            ->add('authorName')
            ->add('isExternal')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
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
            ->add('authorName')
            ->add('isExternal')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var Article $article */
        $article = $this->getSubject();

        $isExternal = (bool)$article->getIsExternal();

        // define group zoning
        $formMapper
            ->with('General', array('class' => 'col-md-4'))->end()
            ->with('Content', array('class' => 'col-md-8'))->end()
        ;

        $imageProperties = [
            'constraints' => [
                new NotBlank(),
            ],
            'disabled' => $isExternal,
        ];

        if ($isExternal) {
            $imageProperties['btn_add'] = false;
            $imageProperties['btn_delete'] = false;
            $imageProperties['btn_list'] = false;
        }

        $formMapper
            ->with('General')
                ->add('isExternal', null, [
                    'required' => false,
                    'disabled' => true,
                ])
                ->add('isDeleted', null, [
                    'label' => 'Scheduled for deletion',
                    'required' => false,
                    'disabled' => true,
                ])
                ->add('title', null, [
                    'disabled' => $isExternal,
                ])
                ->add('category', ModelListType::class, [
                    'required'      => true,
                    'btn_delete'    => false,
                    'btn_add'       => false,
                    'model_manager' => $this->modelManager,
                    'class'         => Category::class,
                ])
                ->add(
                    'image',
                    ModelListType::class,
                    array_merge(
                        $imageProperties,
                        [
                            'sonata_help'   => 'article.help.image',
                            'model_manager' => $this->modelManager,
                            'class'         => Media::class,
                        ]
                    ),
                    [
                        'link_parameters' => [
                            'context'  => OxaMediaInterface::CONTEXT_ARTICLE,
                            'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        ],
                    ]
                )
                ->add('isPublished')
                ->add('isOnHomepage')
                ->add(
                    'slug',
                    null,
                    [
                        'attr' => [
                            'read_only'     => true,
                        ],
                    ]
                )
                ->add('activationDate', DateTimePickerType::class, ['format' => self::FORM_DATETIME_FORMAT])
                ->add('expirationDate', DateTimePickerType::class, [
                    'format'   => self::FORM_DATETIME_FORMAT,
                    'required' => false,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'required' => false,
                    'disabled' => true
                ])
                ->add('updatedUser', TextType::class, [
                    'required' => false,
                    'disabled' => true,
                ])
                ->add('authorName', null, [
                    'required' => false,
                    'disabled' => true,
                ])
            ->end()
            ->with('Content')
                ->add('body', CKEditorType::class, [
                    'required' => true,
                    'disabled' => $isExternal,
                ])
            ->end()
        ;

        if ($isExternal) {
            $property = [
                'by_reference'  => false,
                'required'      => false,
                'btn_add'       => false,
                'type_options' => [
                    'delete'    => false,
                ],
                'attr' => [
                    'read_only'     => true,
                ],
            ];
        } else {
            $property = [
                'by_reference' => false,
                'required' => false,
                'mapped' => true,
            ];
        }
        $formMapper
            ->with('Gallery')
                ->add('images', CollectionType::class, $property, [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'link_parameters' => [
                        'context' => OxaMediaInterface::CONTEXT_ARTICLE_IMAGES,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ]
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
            ->add('authorName')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param Article $article
     */
    public function validate(ErrorElement $errorElement, $article)
    {
        foreach ($article->getImages() as $image) {
            if (!$image->getMedia()) {
                $errorElement->with('images')
                    ->addViolation($this->getTranslator()->trans(
                        'form.article.empty_images',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
                break;
            }
        }
    }

    /**
     * @param Article $entity
     */
    public function prePersist($entity)
    {
        $entity = $this->preSave($entity);
        $entity = $this->setAuthorName($entity);
    }

    /**
     * @param Article $entity
     */
    public function preUpdate($entity)
    {
        $this->preSave($entity);
        parent::preUpdate($entity);
    }

    /**
     * @param Article $entity
     *
     * @return Article
     */
    private function preSave($entity)
    {
        $entity = $this->setSeoDate($entity);

        return $entity;
    }

    /**
     * @param Article $entity
     *
     * @return Article
     */
    private function setSeoDate($entity)
    {

        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName          = $seoSettings['company_name'];
        $titleMaxLength       = $seoSettings['title_max_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];

        $seoTitle = $entity->getTitle() . ' | ' . $companyName;
        $seoDescription = strip_tags($entity->getBody());

        $seoTitle       = mb_substr($seoTitle, 0, $titleMaxLength);
        $seoDescription = mb_substr($seoDescription, 0, $descriptionMaxLength);

        $entity->setSeoTitle($seoTitle);
        $entity->setSeoDescription($seoDescription);

        return $entity;
    }

    /**
     * @param Article $entity
     *
     * @return Article
     */
    private function setAuthorName($entity)
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();
        $user = $container->get('security.token_storage')->getToken()->getUser();

        $authorName = $user->getFullName();

        $entity->setAuthorName($authorName);

        return $entity;
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('show')
            ->add('restore')
        ;
    }

    /**
     * @param string $name
     * @param null $object
     *
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object and in_array($name, $deniedActions) and $object->getIsExternal()) {
            return false;
        }

        return parent::isGranted($name, $object);
    }
}
