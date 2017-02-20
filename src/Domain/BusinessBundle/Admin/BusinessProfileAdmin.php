<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\BusinessBundle\Util\Traits\VideoUploadTrait;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Form\Type\VideoMediaType;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class BusinessProfileAdmin
 * @package Domain\BusinessBundle\Admin
 */
class BusinessProfileAdmin extends OxaAdmin
{
    use VideoUploadTrait;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('city')
            ->add('state')
            ->add('country')
            ->add('catalogLocality')
            ->add(
                'phones.phone',
                null,
                [
                    'label' => $this->trans('filter.label_phone', [], $this->getTranslationDomain())
                ]
            )
            ->add('subscriptions.subscriptionPlan', null, [
                'label' => $this->trans('filter.label_subscription_plan', [], $this->getTranslationDomain())
            ])
            ->add('registrationDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
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
            ->addIdentifier('name')
            ->add('city')
            ->add('state')
            ->add('country')
            ->add('catalogLocality', null, [
                'sortable' => true,
                'sort_field_mapping'=> ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'catalogLocality']]
            ])
            ->add('phones')
            ->add('subscriptionPlan', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/list_subscription.html.twig'
            ])
            ->add('registrationDate')
            ->add('isActive')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getSubject();

        $subscriptionPlan = $businessProfile->getSubscription() ?
            $businessProfile->getSubscription()->getSubscriptionPlan() : new SubscriptionPlan();

        // define group zoning
        $formMapper
            ->tab('Profile', array('class' => 'col-md-6'))
                ->with('General', array('class' => 'col-md-4'))->end()
                ->with('Description', array('class' => 'col-md-8'))->end()
                ->with('Address', array('class' => 'col-md-4'))->end()
                ->with('Map', array('class' => 'col-md-8'))->end()
                ->with('Categories', array('class' => 'col-md-6'))->end()
                ->with('Social Networks', array('class' => 'col-md-6'))->end()
                ->with('Gallery')->end()
            ->end();

            if ($subscriptionPlan->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
                $formMapper->tab('Profile')->with('Video')->end()->end();
            }

            $formMapper->tab('Profile')->with('Status', array('class' => 'col-md-6'))->end()
                ->with('Subscriptions')->end()
                ->with('Coupons', array('class' => 'col-md-6'))->end()
                ->with('Discount', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Reviews', array('class' => 'col-md-6'))
                ->with('User Reviews')->end()
            ->end();

        $oxaConfig = $this->getConfigurationPool()
            ->getContainer()
            ->get('oxa_config');

        if ($this->getSubject()->getLatitude() && $this->getSubject()->getLongitude()) {
            $latitude   = $this->getSubject()->getLatitude();
            $longitude  = $this->getSubject()->getLongitude();
        } else {
            $latitude   = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE);
            $longitude  = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE);
        }

        $em = $this->modelManager->getEntityManager(User::class);

        $query = $em->createQueryBuilder('u')
            ->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.role != :consumerRole')
            ->setParameter('consumerRole', Group::CODE_CONSUMER)
        ;

        $milesOfMyBusinessFieldOptions = [
            'required' => true,
        ];

        $localitiesFieldOptions = [
            'multiple' => true,
            'required' => true,
            'label' => 'Localities',
            'query_builder' => function (\Domain\BusinessBundle\Repository\LocalityRepository $rep) {
                return $rep->getAvailableLocalitiesQb();
            },
        ];

        $neighborhoodsFieldOptions = [
            'multiple' => true,
            'required' => false,
            'label' => 'Neighborhoods',
            'query_builder' => function (\Domain\BusinessBundle\Repository\NeighborhoodRepository $rep) {
                return $rep->getAvailableNeighborhoodsQb();
            },
        ];

        if ($businessProfile->getServiceAreasType() === BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            $localitiesFieldOptions['attr']['disabled'] = 'disabled';
            $localitiesFieldOptions['required'] = false;

            $neighborhoodsFieldOptions['attr']['disabled'] = 'disabled';
        } else {
            $milesOfMyBusinessFieldOptions['attr']['disabled'] = 'disabled';
            $milesOfMyBusinessFieldOptions['required'] = false;
        }

        $categories1 = $businessProfile->getCategory();
        $categories2 = $businessProfile->getSubcategories(Category::CATEGORY_LEVEL_2);
        $categories3 = $businessProfile->getSubcategories(Category::CATEGORY_LEVEL_3);

        $formMapper
            ->tab('Profile')
                ->with('General')
                    ->add('name')
                    ->add('user', 'sonata_type_model', [
                        'required' => false,
                        'btn_delete' => false,
                        'btn_add' => false,
                        'query' => $query,
                    ])
                    ->add('logo', 'sonata_type_model_list', [
                        'required' => false
                    ], ['link_parameters' => [
                        'context' => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ]])
                    ->add('background', 'sonata_type_model_list', [
                        'required' => false
                    ], ['link_parameters' => [
                        'context' => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ]])
                    ->add('website')
                    ->add('email', EmailType::class, [
                        'required' => false,
                    ])
                    ->add('slug', null, ['read_only' => true, 'required' => false])
                ->end()
                ->with('Description')
                    ->add('slogan')
                    ->add('product')
                    ->add('description', null, [
                        'attr' => [
                            'rows' => 5,
                        ],
                    ])
                    ->add('workingHours')
                    ->add(
                        'collectionWorkingHours',
                        'sonata_type_collection',
                        [
                            'by_reference' => false,
                            'required' => false,
                        ],
                        [
                            'edit' => 'inline',
                            'delete_empty' => false,
                            'inline' => 'table',
                        ]
                    )
                    ->add(
                        'phones',
                        'sonata_type_collection',
                        [
                            'by_reference' => false,
                            'required' => false,
                        ],
                        [
                            'edit' => 'inline',
                            'delete_empty' => false,
                            'inline' => 'table',
                        ]
                    )
                ->end()
                ->with('Address')
                    ->add('country', 'sonata_type_model_list', [
                        'required' => true,
                        'btn_delete' => false,
                        'btn_add' => false,
                    ])
                    ->add('state')
                    ->add('city', null, [
                        'required' => true
                    ])
                    ->add('catalogLocality', 'sonata_type_model_list', [
                        'required' => true,
                        'btn_delete' => false,
                        'btn_add' => false,
                    ])
                    ->add('zipCode', null, [
                        'required' => true
                    ])
                    ->add('streetAddress', null, [
                        'required' => true
                    ])
                    ->add('customAddress')
                    ->add('hideAddress')
                ->end()
                ->with('Map')
                    ->add('useMapAddress', null, [
                        'label' => $this->trans('form.label_useMapAddress', [], $this->getTranslationDomain())
                    ])
                    ->add('latitude')
                    ->add('longitude')
                    ->add('googleAddress', 'google_map', [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ])
                ->end()
                ->with('Social Networks')
                    ->add('twitterURL')
                    ->add('facebookURL')
                    ->add('googleURL')
                    ->add('youtubeURL')
                ->end()
                ->with('Categories')
                    ->add('categories', null, [
                        'label' => 'Category lvl 1',
                        'multiple' => false,
                        'required' => true,
                        'query_builder' => function (\Domain\BusinessBundle\Repository\CategoryRepository $rep) {
                            return $rep->getAvailableParentCategoriesQb();
                        },
                        'data' => $categories1,
                    ])
                    ->add('categories2', EntityType::class, [
                        'label' => 'Categories lvl 2',
                        'multiple' => true,
                        'required' => false,
                        'query_builder' => function (\Domain\BusinessBundle\Repository\CategoryRepository $rep) {
                            return $rep->getAvailableChildCategoriesQb(Category::CATEGORY_LEVEL_2);
                        },
                        'data' => $categories2,
                        'mapped' => false,
                        'class' => \Domain\BusinessBundle\Entity\Category::class,
                    ])
                    ->add('categories3', EntityType::class, [
                        'label' => 'Categories lvl 3',
                        'multiple' => true,
                        'required' => false,
                        'query_builder' => function (\Domain\BusinessBundle\Repository\CategoryRepository $rep) {
                            return $rep->getAvailableChildCategoriesQb(Category::CATEGORY_LEVEL_3);
                        },
                        'data' => $categories3,
                        'mapped' => false,
                        'class' => \Domain\BusinessBundle\Entity\Category::class,
                    ])
                    ->add('brands', null, ['required' => false])
                    ->add('areas', null, ['multiple' => true, 'required' => false])
                    ->add('serviceAreasType', ChoiceType::class, [
                        'choices' => BusinessProfile::getServiceAreasTypes(),
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                    ])
                    ->add('milesOfMyBusiness', null, $milesOfMyBusinessFieldOptions)
                    ->add('localities', null, $localitiesFieldOptions)
                    ->add('neighborhoods', null, $neighborhoodsFieldOptions)
                    ->add('paymentMethods', null, [
                        'multiple' => true,
                        'expanded' => true,
                    ])
                ->end()
                ->with('Gallery')
                    ->add('images', 'sonata_type_collection', [
                        'by_reference' => false,
                        'required' => false,
                        'mapped' => true,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => [
                            'context' => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES,
                            'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        ]
                    ])
                ->end()
            ->end();


        if ($subscriptionPlan->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            if (!$businessProfile->getVideo()) {
                $formMapper
                    ->tab('Profile')
                        ->with('Video')
                            ->add('videoFile', FileType::class, [
                                'attr' => [
                                    'accept' => 'webm, mp4, ogg, video/webm, video/mp4, video/ogg',
                                ],
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                            ])
                            ->add('videoUrl', TextType::class, [
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                            ])
                            ->add('videoTitle', TextType::class, [
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                                'constraints' => array(
                                    new Length(
                                        [
                                            'max' => VideoMedia::VIDEO_TITLE_MAX_LENGTH,
                                        ]
                                    ),
                                ),
                            ])
                            ->add('videoDescription', TextareaType::class, [
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                                'constraints' => array(
                                    new Length(
                                        [
                                            'max' => VideoMedia::VIDEO_TITLE_MAX_DESCRIPTION,
                                        ]
                                    ),
                                ),
                            ])
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Profile')
                        ->with('Video')
                            ->add('removeVideo', CheckboxType::class, [
                                'mapped' => false,
                                'required' => false,
                            ])
                            ->add('videoName', TextType::class, [
                                'mapped' => false,
                                'required' => false,
                                'attr' => [
                                    'value' => $businessProfile->getVideo()->getName(),
                                ],
                            ])
                            ->add('videoFile', FileType::class, [
                                'attr' => [
                                    'accept' => 'webm, mp4, ogg, video/webm, video/mp4, video/ogg',
                                    'data-hidden-field' => true,
                                ],
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                            ])
                            ->add('videoUrl', TextType::class, [
                                'attr' => [
                                    'data-hidden-field' => true,
                                ],
                                'data_class' => null,
                                'mapped' => false,
                                'required' => false,
                            ])
                            ->add('videoTitle', TextType::class, [
                                'mapped' => false,
                                'required' => false,
                                'attr' => [
                                    'value' => $businessProfile->getVideo()->getTitle(),
                                ],
                                'constraints' => array(
                                    new Length(
                                        [
                                            'max' => VideoMedia::VIDEO_TITLE_MAX_LENGTH,
                                        ]
                                    ),
                                ),
                            ])
                            ->add('videoDescription', TextareaType::class, [
                                'mapped' => false,
                                'required' => false,
                                'data' => $businessProfile->getVideo()->getDescription(),
                                'constraints' => array(
                                    new Length(
                                        [
                                            'max' => VideoMedia::VIDEO_TITLE_MAX_LENGTH,
                                        ]
                                    ),
                                ),
                            ])
                        ->end()
                    ->end()
                ;
            }
        }

        $formMapper
            ->tab('Profile')
                ->with('Status')
                    ->add('isActive')
                    ->add('updatedAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                    ->add('updatedUser', 'sonata_type_model', [
                        'required' => false,
                        'btn_add' => false,
                        'disabled' => true,
                    ])
                    ->add('createdAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                    ->add('createdUser', 'sonata_type_model', [
                        'required' => false,
                        'btn_add' => false,
                        'disabled' => true,
                    ])
                ->end()
                ->with('Subscriptions')
                    ->add('subscriptions', 'sonata_type_collection', [
                        'by_reference' => false,
                        'required' => true,
                        'type_options' => [
                            'delete' => true,
                            'delete_options' => [
                                'type' => 'checkbox',
                                'type_options' => ['mapped' => false, 'required' => false]
                            ]]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'allow_delete' => false,
                    ])
                ->end()
                ->with('Coupons')
                    ->add('coupons', 'sonata_type_collection', [
                        'by_reference' => false,
                        'required' => false,
                        'type_options' => [
                            'delete' => true,
                            'delete_options' => [
                                'type' => 'checkbox',
                                'type_options' => ['mapped' => false, 'required' => false]
                            ]]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'allow_delete' => false,
                    ])
                ->end()
                ->with('Discount')
                    ->add('discount', 'ckeditor', [
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('Reviews')
                ->with('User Reviews')
                    ->add('businessReviews', 'sonata_type_collection', [
                        'by_reference' => false,
                        'mapped' => true,
                        'btn_add' => false,
                        'disabled' => true,
                        'type_options' => [
                            'delete' => false,
                        ]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'allow_delete' => false,
                    ])
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
            ->add('logo', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_image.html.twig'
            ])
            ->add('background', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_background.html.twig'
            ])
            ->add('name')
            ->add('images')
            ->add('user')
            ->add('subscription')
            ->add('subscriptions')
            ->add('discount')
            ->add('coupons')
            ->add('category', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_category.html.twig'
            ])
            ->add('categories2', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_subcategories.html.twig'
            ])
            ->add('categories3', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_subcategories.html.twig'
            ])
            ->add('catalogLocality')
            ->add('areas')
            ->add('localities')
            ->add('neighborhoods')
            ->add('brands')
            ->add('paymentMethods')
            ->add('businessReviews')
            ->add('website')
            ->add('email')
            ->add('phones')
            ->add('registrationDate')
            ->add('slogan')
            ->add('description', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_description.html.twig'
            ])
            ->add('product')
            ->add('collectionWorkingHours', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours_collection.html.twig'
            ])
            ->add('workingHours', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours.html.twig'
            ])
            ->add('hideAddress')
            ->add('slug')
            ->add('updatedAt')
            ->add('updatedUser')
            ->add('isActive')
        ;
    }

    public function setTemplate($name, $template)
    {
        $this->templates['edit'] = 'DomainBusinessBundle:Admin:edit.html.twig';
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     * @return null
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var BusinessProfile $object */

        if ($object->getUseMapAddress()) {
            if (!$object->getGoogleAddress()) {
                $errorElement->with('googleAddress')
                    ->addViolation($this->getTranslator()->trans(
                        'form.google_address.required',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
                return null;
            }

            $addressManager = $this->configurationPool
                ->getContainer()
                ->get('domain_business.manager.address_manager');

            $addressResult = $addressManager->validateCoordinates($object->getLatitude(), $object->getLongitude());

            if (!empty($addressResult['error'])) {
                $errorMessage = $this->getTranslator()->trans(
                    'form.google_address.invalid',
                    [],
                    $this->getTranslationDomain()
                );

                $errorElement
                    ->with('latitude')
                        ->addViolation($errorMessage)
                    ->end()
                    ->with('longitude')
                        ->addViolation($errorMessage)
                    ->end()
                ;
            } else {
                $addressManager->setGoogleAddress($addressResult['result'], $object);
            }
        } else {
            $errorElement
                ->with('country')
                ->end()
                ->with('streetAddress')
                ->end()
                ->with('city')
                ->end()
                ->with('zipCode')
                ->end()
            ;
        }

        // check if user try to upload images more, that allowed
        if (count($object->getImages()) > BusinessGallery::MAX_IMAGES_PER_BUSINESS) {
            $errorElement->with('images')
                ->addViolation($this->getTranslator()->trans(
                    'form.business.max_images',
                    [
                        'max_images_per_business' => BusinessGallery::MAX_IMAGES_PER_BUSINESS
                    ],
                    $this->getTranslationDomain()
                ))
                ->end()
            ;
        }

        // check if gallery records have not empty Media field
        foreach ($object->getImages() as $image) {
            if (!$image->getMedia()) {
                $errorElement->with('images')
                    ->addViolation($this->getTranslator()->trans(
                        'form.business.empty_images',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
                break;
            }
        }

        foreach ($object->getSubscriptions() as $subscription) {
            if ($subscription->getStartDate() > new \DateTime('now')) {
                $errorElement->with('subscriptions')
                    ->addViolation($this->getTranslator()->trans(
                        'form.subscription.start_date',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
            }
        }

        if (!trim($object->getName())) {
            $errorElement->with('name')
                ->addViolation($this->getTranslator()->trans(
                    'form.name.required',
                    [],
                    $this->getTranslationDomain()
                ))
                ->end()
            ;
        }

        if (!$object->getCollectionWorkingHours()->isEmpty()) {
            if (!DayOfWeekModel::validateWorkingHoursTime($object->getCollectionWorkingHours())) {
                $errorElement->with('collectionWorkingHours')
                    ->addViolation($this->getTranslator()->trans(
                        'form.collectionWorkingHours.duration',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
            }

            if (!DayOfWeekModel::validateWorkingHoursOverlap($object->getCollectionWorkingHours())) {
                $errorElement->with('collectionWorkingHours')
                    ->addViolation($this->getTranslator()->trans(
                        'form.collectionWorkingHours.overlap',
                        [],
                        $this->getTranslationDomain()
                    ))
                    ->end()
                ;
            }
        }
    }

    /**
     * Modify list results
     *
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $parameters = $this->getFilterParameters();

        // search by active subscription of chosen subscriptionPlan
        if (isset($parameters['subscriptions__subscriptionPlan']) &&
            !empty($parameters['subscriptions__subscriptionPlan']['value'])
        ) {
            $subscriptionPlanId = $parameters['subscriptions__subscriptionPlan']['value'];

            $query->leftJoin($query->getRootAliases()[0] . '.subscriptions', 's');
            $query->leftJoin('s.subscriptionPlan', 'sp');

            $query->andWhere('sp.id = :subscriptionPlanId');
            $query->andWhere('s.status = :subscriptionStatus');

            $query->setParameter('subscriptionPlanId', $subscriptionPlanId);
            $query->setParameter('subscriptionStatus', StatusInterface::STATUS_ACTIVE);
        }

        return $query;
    }

    public function prePersist($entity)
    {
        $this->preSave($entity);
    }

    public function preUpdate($entity)
    {
        /** @var BusinessProfile $entity */
        $this->preSave($entity);
    }

    public function postPersist($entity)
    {
        $this->createFreeSubscription($entity);
    }

    private function preSave($entity)
    {
        $entity = $this->setSearchValues($entity);
        $entity = $this->setVideoValue($entity);
        $entity = $this->setSubcategories($entity);
        $entity = $this->setSeoDate($entity);
    }

    private function createFreeSubscription($entity)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');

        $subscriptionStatusManager = $container->get('domain_business.manager.subscription_status_manager');

        $subscription = $subscriptionStatusManager->manageBusinessSubscriptionCreate($entity, $em);

        $em->flush($subscription);
    }

    private function setSearchValues($entity)
    {
        $nameEn = '';
        $nameEs = '';

        $descEn = '';
        $descEs = '';

        if ($entity->getLocale() == 'en') {
            $nameEn = $entity->getName();

            if (!$entity->getNameEs()) {
                $nameEs = $entity->getName();
            }

            $descEn = $entity->getDescription();

            if ($descEn and !$entity->getDescriptionEs()) {
                $descEs = $descEn;
            }
        } else {
            $nameEs = $entity->getName();

            if (!$entity->getNameEn()) {
                $nameEn = $entity->getName();
            }

            $descEs = $entity->getDescription();

            if ($descEs and !$entity->getDescriptionEn()) {
                $descEn = $descEs;
            }
        }

        if ($nameEn) {
            $entity->setNameEn($nameEn);
        }

        if ($nameEs) {
            $entity->setNameEs($nameEs);
        }

        if ($descEn) {
            $entity->setDescriptionEn($descEn);
        }

        if ($descEs) {
            $entity->setDescriptionEs($descEs);
        }

        return $entity;
    }

    private function setVideoValue($entity)
    {
        $form = $this->getForm();
        $container = $this->getConfigurationPool()->getContainer();

        /** @var Request $request */
        $request = Request::createFromGlobals();
        $files = current($request->files->all());

        if ($form->has('removeVideo') && $form->get('removeVideo')->getData()) {
            $entity->setVideo(null);
            $container->get('oxa.manager.video')->removeMedia($entity->getVideo()->getId());
        }

        if ($files) {
            $videoMediaData = $this->uploadVideo($entity);

            if ($videoMediaData) {
                $videoMediaData->setTitle($form->get('videoTitle')->getData());
                $videoMediaData->setDescription($form->get('videoDescription')->getData());
                $videoMediaData->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);

                $entity->setVideo($videoMediaData);
            } else {
                /* @var $video VideoMedia */
                $video = $entity->getVideo();

                if ($video) {
                    $video->setName($form->get('videoName')->getData());
                    $video->setTitle($form->get('videoTitle')->getData());
                    $video->setDescription($form->get('videoDescription')->getData());

                    if ($video->getYoutubeSupport() and !$video->getYoutubeAction()) {
                        $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_UPDATE);
                    }
                }
            }
        }

        return $entity;
    }

    private function uploadVideo(BusinessProfile $businessProfile)
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        /** @var Request $request */
        $request = Request::createFromGlobals();

        $media = null;
        $files = current($request->files->all());

        if ($files) {
            if (!isset($files['videoFile']) || (isset($files['videoFile']) && $files['videoFile'] == null)) {
                $form = $this->getForm();

                if ($form->has('videoUrl') && $form->get('videoUrl')->getData()) {
                    try {
                        $media = $container->get('oxa.manager.video')->uploadRemoteFile($form->get('videoUrl')->getData());
                    } catch (\Exception $e) {
                        $media = null;
                    }
                }

                return $media;
            }

            try {
                $media = $container->get('oxa.manager.video')->uploadLocalFile(current($files));
            } catch (\Exception $e) {
                $media = null;
            }
        }

        return $media;
    }

    private function getMediaUploadDirectory()
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        return $container->getParameter('videos_upload_path');
    }

    /**
     * @param BusinessProfile $entity
     *
     * @return BusinessProfile
     */
    private function setSubcategories($entity)
    {
        $categories2 = $this->getForm()->get('categories2')->getData();
        $categories3 = $this->getForm()->get('categories3')->getData();

        foreach ($categories2 as $subcategory) {
            $entity->addCategory($subcategory);
        }

        foreach ($categories3 as $subcategory) {
            $entity->addCategory($subcategory);
        }

        return $entity;
    }

    /**
     * @param BusinessProfile $entity
     *
     * @return BusinessProfile
     */
    private function setSeoDate($entity)
    {
        /** @var ContainerInterface $container */
        $container    = $this->getConfigurationPool()->getContainer();

        $seoTitle       = BusinessProfileUtil::seoTitleBuilder($entity, $container);
        $seoDescription = BusinessProfileUtil::seoDescriptionBuilder($entity, $container);

        $entity->setSeoTitle($seoTitle);
        $entity->setSeoDescription($seoDescription);

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
            ->add('show')
            ->add('move', $this->getRouterIdParameter().'/move/{position}')
        ;
    }

    public function getExportFormats()
    {
        return [
            'business_profile.admin.export.csv' => 'csv',
        ];
    }

    public function getExportFields()
    {
        $exportFields['ID']   = 'id';
        $exportFields['Name'] = 'nameEn';
        $exportFields['Slug'] = 'slug';
        $exportFields['Level 1 category ID']   = 'category.id';
        $exportFields['Level 1 category name'] = 'category.name';
        $exportFields['Level 2 name+ID']       = 'exportCategoryLvl2';
        $exportFields['Level 3 name+ID']       = 'exportCategoryLvl3';

        return $exportFields;
    }
}
