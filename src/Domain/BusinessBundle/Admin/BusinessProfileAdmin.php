<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfilePhoneTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
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
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

/**
 * Class BusinessProfileAdmin
 * @package Domain\BusinessBundle\Admin
 */
class BusinessProfileAdmin extends OxaAdmin
{
    const DATE_PICKER_FORMAT = 'yyyy-MM-dd';
    const DATE_PICKER_REPORT_FORMAT = 'YYYY-MM-DD';

    /**
     * @var bool
     */
    public $copyAvailable = true;

    /**
     * @var bool
     */
    public $allowBatchRestore = true;

    /**
     * @return BusinessProfile
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $container = $this->getConfigurationPool()->getContainer();

        if ($this->getRequest()->getMethod() == Request::METHOD_GET) {
            $parentId = $this->getRequest()->get('id', null);

            if ($parentId) {
                $parent    = $container->get('doctrine')->getRepository(BusinessProfile::class)->find($parentId);

                if ($parent) {
                    $instance = $this->cloneParentEntity($parent);
                }
            }
        }

        if (!$instance->getCountry()) {
            $country = $container->get('domain_business.manager.business_profile')->getDefaultProfileCountry();

            if ($country) {
                $instance->setCountry($country);
            }
        }

        return $instance;
    }

    /**
     * @param string                  $action
     * @param BusinessProfile|null    $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        $accessToCopy = [
            'create',
            'edit',
        ];

        if ($object and in_array($action, $accessToCopy)) {
            $list['copy'] = [
                'template' => 'DomainBusinessBundle:Admin/BusinessProfile:copy_action_button.html.twig',
            ];
        }

        return $list;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, [
                'show_filter' => true,
            ])
            ->add('city')
            ->add('catalogLocality')
            ->add(
                'user',
                null,
                [
                    'label' => 'Business Admin',
                ]
            )
            ->add(
                'phones.phone',
                null,
                [
                    'label' => $this->trans('filter.label_phone', [], $this->getTranslationDomain())
                ]
            )
            ->add('hasImages')
            ->add('subscriptions.subscriptionPlan', null, [
                'label' => $this->trans('filter.label_subscription_plan', [], $this->getTranslationDomain())
            ])
            ->add('registrationDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
            ->add('isActive')
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
            ->addIdentifier('name')
            ->add('city')
            ->add('catalogLocality', null, [
                'sortable' => true,
                'sort_field_mapping'=> ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'catalogLocality']]
            ])
            ->add('phones', null, [
                'template' => 'OxaSonataAdminBundle:ListFields:list_orm_one_to_many.html.twig',
            ])
            ->add('hasImages')
            ->add('subscriptionPlan', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/list_subscription.html.twig'
            ])
            ->add('registrationDate')
            ->add(
                'user',
                null,
                [
                    'label' => 'Business Admin',
                ]
            )
            ->add('isActive')
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
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getSubject();

        $businessProfile->setLocale(LocaleHelper::DEFAULT_LOCALE);

        $subscriptionPlanCode = $businessProfile->getSubscriptionPlanCode();

        // define tabs
        $formMapper
            ->tab('Main')->end()
            ->tab('Media')->end()
            ->tab('Others')->end()
            ->tab('Legacy URLs')->end()
        ;

        // define block
        $formMapper
            ->tab('Main')
                ->with('Subscription')->end()
                ->with('Common')->end()
            ->end()
        ;

        // form translatable block
        foreach (LocaleHelper::getLocaleList() as $key => $value) {
            $formMapper->tab('Main')->with($value, ['class' => 'col-md-6',])->end()->end();
        }

        $formMapper
            ->tab('Main')
                ->with('Main')->end()
                ->with('Payments method')->end()
                ->with('Addresses', ['class' => 'col-md-6',])->end()
                ->with('Map', ['class' => 'col-md-6',])->end()
                ->with('Category')->end()
            ->end()
            ->tab('Media')
                ->with('Social Networks')->end()
                ->with('Gallery')->end()
                ->with('Panorama')->end()
            ->end()
            ->tab('Others')
                ->with('Coupons')->end()
                ->with('Discount')->end()
                ->with('DoubleClick')->end()
            ->end()
                ->tab('Legacy URLs')
                    ->with('Slugs')->end()
            ->end()
        ;


        if ($businessProfile->getId() and $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            $formMapper->tab('Main')->with('SuperVM')->end()->end();
            $formMapper->tab('Media')->with('Video')->end()->end();
        }

        if ($businessProfile->getId() and $subscriptionPlanCode > SubscriptionPlanInterface::CODE_FREE) {
            $formMapper->tab('Main')->with('Keywords')->end()->end();
        }

        // Main tab
        // Subscription Block
        $formMapper
            ->tab('Main')
                ->with('Subscription')
                    ->add(
                        'subscriptions',
                        'sonata_type_collection',
                        [
                            'by_reference'  => false,
                            'required'      => true,
                            'type_options' => [
                                'delete'         => true,
                                'delete_options' => [
                                    'type'         => 'checkbox',
                                    'type_options' => [
                                        'mapped'   => false,
                                        'required' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'edit'          => 'inline',
                            'inline'        => 'table',
                            'allow_delete'  => false,
                        ]
                    )
                    ->add('isActive')
                ->end()
            ->end()
        ;

        $formMapper
            ->tab('Main')
                ->with('Common')
                    ->add('name')
                ->end()
            ->end()
        ;

        // Translatable Block
        foreach (LocaleHelper::getLocaleList() as $key => $value) {
            $formMapper
                ->tab('Main')
                ->with($value)
            ;

            $this->addTranslationBlock($formMapper, $businessProfile, $key);

            $formMapper->end()->end();
        }

        // Main Block
        $formMapper
            ->tab('Main')
                ->with('Main')
                    ->add('website', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('actionUrlType', ChoiceType::class, [
                        'choices'  => BusinessProfile::getActionUrlTypes(),
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                        'translation_domain' => 'AdminDomainBusinessBundle',
                    ])
                    ->add('actionUrl', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('email', EmailType::class, [
                        'required' => false,
                    ])
                    ->add('slug', null, [
                        'read_only' => true,
                        'required'  => false,
                    ])
                    ->add(
                        'collectionWorkingHours',
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
                    ->add(BusinessProfileWorkingHourTypeValidator::ERROR_BLOCK_PATH, TextType::class, [
                        'label_attr' => [
                            'hidden' => true,
                        ],
                        'mapped'   => false,
                        'required' => false,
                        'attr' => [
                            'class' => 'hidden',
                        ],
                    ])
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
                    ->add(BusinessProfilePhoneTypeValidator::ERROR_BLOCK_PATH, TextType::class, [
                        'label_attr' => [
                            'hidden' => true,
                        ],
                        'mapped'   => false,
                        'required' => false,
                        'attr' => [
                            'class' => 'hidden',
                        ],
                    ])
                ->end()
            ->end()
        ;

        // Payment Method Block
        $formMapper
            ->tab('Main')
                ->with('Payments method')
                    ->add('paymentMethods', null, [
                        'multiple' => true,
                        'expanded' => true,
                    ])
                ->end()
            ->end()
        ;

        // Addresses Block
        $formMapper
            ->tab('Main')
                ->with('Addresses')
                    ->add('city', null, [
                        'required' => true,
                    ])
                    ->add('catalogLocality', 'sonata_type_model_list', [
                        'required'      => true,
                        'btn_delete'    => false,
                        'btn_add'       => false,
                    ])
                    ->add('zipCode', null, [
                        'required' => true
                    ])
                    ->add('streetAddress', null, [
                        'required' => true,
                    ])
                    ->add('customAddress')
                    ->add('hideAddress')
                    ->add('hideMap')
                ->end()
            ->end()
        ;

        // Map Block
        $oxaConfig = $this->getConfigurationPool()->getContainer()->get('oxa_config');

        if ($this->getSubject()->getLatitude() && $this->getSubject()->getLongitude()) {
            $latitude   = $this->getSubject()->getLatitude();
            $longitude  = $this->getSubject()->getLongitude();
        } else {
            $latitude   = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE);
            $longitude  = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE);
        }

        $formMapper
            ->tab('Main')
                ->with('Map')
                ->add('useMapAddress', null, [
                    'label' => $this->trans('form.label_useMapAddress', [], $this->getTranslationDomain())
                ])
                ->add('latitude')
                ->add('longitude')
                ->add('googleAddress', 'google_map', [
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ])
                ->end()
            ->end()
        ;

        // Category Block
        $milesOfMyBusinessFieldOptions = [
            'required' => true,
        ];

        $areasFieldOptions = [
            'multiple' => true,
            'required' => true,
            'attr' => [
                'data-select-all' => true,
            ],
        ];

        $localitiesFieldOptions = [
            'multiple' => true,
            'required' => true,
            'label' => 'Localities',
            'attr' => [
                'data-select-all' => true,
            ],
            'query_builder' => function (\Domain\BusinessBundle\Repository\LocalityRepository $rep) {
                return $rep->getAvailableLocalitiesQb();
            },
        ];

        $neighborhoodsFieldOptions = [
            'multiple' => true,
            'required' => false,
            'label' => 'Neighborhoods',
            'attr' => [
                'data-select-all' => true,
            ],
            'query_builder' => function (\Domain\BusinessBundle\Repository\NeighborhoodRepository $rep) {
                return $rep->getAvailableNeighborhoodsQb();
            },
        ];

        if ($businessProfile->getServiceAreasType() === BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            $localitiesFieldOptions['attr']['disabled'] = 'disabled';
            $localitiesFieldOptions['required'] = false;

            $areasFieldOptions['attr']['disabled'] = 'disabled';
            $areasFieldOptions['required'] = false;

            $neighborhoodsFieldOptions['attr']['disabled'] = 'disabled';
        } else {
            $milesOfMyBusinessFieldOptions['attr']['disabled'] = 'disabled';
            $milesOfMyBusinessFieldOptions['required'] = false;
        }

        $formMapper
            ->tab('Main')
                ->with('Category')
                    ->add('categories', 'sonata_type_model_autocomplete', [
                        'property' => [
                            'name',
                            'searchTextEs',
                        ],
                        'multiple' => true,
                        'required' => true,
                    ])
                    ->add('serviceAreasType', ChoiceType::class, [
                        'choices' => BusinessProfile::getServiceAreasTypes(),
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                    ])
                    ->add('milesOfMyBusiness', null, $milesOfMyBusinessFieldOptions)
                    ->add('areas', null, $areasFieldOptions)
                    ->add('localities', null, $localitiesFieldOptions)
                    ->add('neighborhoods', null, $neighborhoodsFieldOptions)
                ->end()
            ->end()
        ;

        // Super VM Block
        if ($businessProfile->getId() and $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            $formMapper
                ->tab('Main')
                    ->with('SuperVM')
                        ->add(
                            'extraSearches',
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
                ->end()
            ;
        }

        // Keyword Block
        if ($businessProfile->getId() and $subscriptionPlanCode > SubscriptionPlanInterface::CODE_FREE) {
            $formMapper
                ->tab('Main')
                    ->with('Keywords')
                        ->add('keywordText', TextType::class, [
                            'attr' => [
                                'class' => 'selectize-control',
                            ],
                            'required' => false,
                        ])
                    ->end()
                ->end()
            ;
        }

        // Media tab
        // Social Networks Block
        $formMapper
            ->tab('Media')
                ->with('Social Networks')
                    ->add('twitterURL', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('facebookURL', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('googleURL', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('youtubeURL', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('instagramURL', UrlType::class, [
                        'required' => false,
                    ])
                    ->add('tripAdvisorURL', UrlType::class, [
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;

        // Gallery Block
        $formMapper
            ->tab('Media')
                ->with('Gallery')
                    ->add(
                        'images',
                        'sonata_type_collection',
                        [
                            'by_reference' => false,
                            'required'     => false,
                            'mapped'       => true,
                        ],
                        [
                            'edit'      => 'inline',
                            'inline'    => 'table',
                            'link_parameters' => [
                                'context'   => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES,
                                'provider'  => OxaMediaInterface::PROVIDER_IMAGE,
                            ]
                        ]
                    )
                    ->add(
                        'logo',
                        'sonata_type_model_list',
                        [
                            'required' => false,
                        ],
                        [
                            'link_parameters' => [
                                'context'  => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO,
                                'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                            ]
                        ]
                    )
                    ->add(
                        'background',
                        'sonata_type_model_list',
                        [
                            'required' => false,
                        ],
                        [
                            'link_parameters' => [
                                'context'  => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
                                'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                            ],
                        ]
                    )
                ->end()
            ->end()
        ;

        // Panorama Block
        $formMapper
            ->tab('Media')
                ->with('Panorama')
                    ->add('panoramaId')
                ->end()
            ->end()
        ;

        // Video Block
        if ($businessProfile->getId() and $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {

            $formMapper
                ->tab('Media')
                    ->with('Video')
                        ->add('video', 'sonata_type_model_list', [
                            'required' => false,
                        ])
                    ->end()
                ->end()
            ;
        }

        // Others Tab
        // Coupons Block
        $formMapper
            ->tab('Others')
                ->with('Coupons')
                    ->add(
                        'coupons',
                        'sonata_type_collection',
                        [
                            'by_reference'  => false,
                            'required'      => false,
                            'type_options' => [
                                'delete'         => true,
                                'delete_options' => [
                                    'type'         => 'checkbox',
                                    'type_options' => [
                                        'mapped'   => false,
                                        'required' => false,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'edit'          => 'inline',
                            'inline'        => 'table',
                            'allow_delete'  => false,
                        ]
                    )
                ->end()
            ->end()
        ;

        // Discount Block
        $formMapper
            ->tab('Others')
                ->with('Discount')
                    ->add('discount', null, [
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;

        // DoubleClick Block
        $formMapper
            ->tab('Others')
                ->with('DoubleClick')
                    ->add('dcOrderId', null, [
                        'label' => 'DC Order Id for Ad Usage Report',
                    ])
                ->end()
            ->end()
        ;

        // Legacy URLs Tab
        if ($this->getSubject()->getId()) {
            $formMapper
                ->tab('Legacy URLs')
                    ->with('Slugs')
                        ->add(
                            'aliases',
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
                ->end()
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        // define tabs
        $showMapper
            ->tab('Main')->end()
            ->tab('Media')->end()
            ->tab('Others')->end()
            ->tab('Legacy URLs')->end()
            ->tab('Reviews')->end()
        ;

        // Main tab
        // Subscription Block
        $showMapper
            ->tab('Main')
                ->with('Subscription')
                    ->add('subscriptions', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                    ->add('isActive')
                ->end()
            ->end()
        ;

        // Translatable Block
        $showMapper
            ->tab('Main')
                ->with('Translatable')
                    ->add('name')
                    ->add('slogan')
                    ->add('description', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_description.html.twig',
                    ])
                    ->add('product')
                    ->add('brands')
                ->end()
            ->end()
        ;

        // Main Block
        $showWorkingHoursCol = 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours_collection.html.twig';

        $showMapper
            ->tab('Main')
                ->with('Main')
                    ->add('id')
                    ->add('user', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                    ])
                    ->add('website')
                    ->add('actionUrlType')
                    ->add('actionUrl')
                    ->add('email')
                    ->add('slug')
                    ->add('collectionWorkingHours', null, [
                        'template' => $showWorkingHoursCol,
                    ])
                    ->add('phones', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Payment Method Block
        $showMapper
            ->tab('Main')
                ->with('Payments method')
                    ->add('paymentMethods', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Addresses Block
        $showMapper
            ->tab('Main')
                ->with('Addresses')
                    ->add('catalogLocality', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                    ])
                    ->add('zipCode')
                    ->add('streetAddress')
                    ->add('customAddress')
                    ->add('hideAddress')
                    ->add('hideMap')
                    ->add('latitude')
                    ->add('longitude')
                ->end()
            ->end()
        ;

        // Category Block
        $showMapper
            ->tab('Main')
                ->with('Categories')
                    ->add('categories', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                    ->add('serviceAreasType')
                    ->add('milesOfMyBusiness')
                    ->add('areas', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                    ->add('localities', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                    ->add('neighborhoods', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Super VM Block
        $showMapper
            ->tab('Main')
                ->with('SuperVM')
                    ->add('extraSearches', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Keyword Block
        $showMapper
            ->tab('Main')
                ->with('Keywords')
                    ->add('keywordText', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_keywords.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Media tab
        // Social Networks Block
        $showMapper
            ->tab('Media')
                ->with('Social Networks')
                    ->add('twitterURL')
                    ->add('facebookURL')
                    ->add('googleURL')
                    ->add('youtubeURL')
                    ->add('instagramURL')
                    ->add('tripAdvisorURL')
                ->end()
            ->end()
        ;

        // Gallery Block
        $showMapper
            ->tab('Media')
                ->with('Gallery')
                    ->add('images', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_one_to_many.html.twig',
                    ])
                    ->add('logo', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_many_to_one.html.twig',
                    ])
                    ->add('background', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_many_to_one.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Panorama Block
        $showMapper
            ->tab('Media')
                ->with('Gallery')
                    ->add('panoramaId', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_panorama.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Video Block
        $showMapper
            ->tab('Media')
                ->with('Video')
                    ->add('video.posterImage', null, [
                        'template' => 'OxaVideoBundle:Admin:show_business_video_image.html.twig'
                    ])
                    ->add('video.reference', null, [
                        'template' => 'OxaVideoBundle:Admin:show_business_video_reference.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Others Tab
        // Coupons Block
        $showMapper
            ->tab('Others')
                ->with('Coupons')
                    ->add('coupons', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_coupon_orm_one_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Discount Block
        $showMapper
            ->tab('Others')
                ->with('Discount')
                    ->add('discount')
                ->end()
            ->end()
        ;

        // DoubleClick Block
        $showMapper
            ->tab('Others')
                ->with('DoubleClick')
                    ->add('dcOrderId', null, [
                        'label' => 'DC Order Id for Ad Usage Report',
                    ])
                ->end()
            ->end()
        ;

        // Legacy URLs Tab
        $showMapper
            ->tab('Legacy URLs')
                ->with('Slugs')
                    ->add('aliases', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Review Tab
        $showMapper
            ->tab('Reviews')
                ->with('User Reviews')
                    ->add('reviewPagination', null, [
                        'label'    => 'Pagination',
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_review_pagination.html.twig',
                    ])
                    ->add('reviewList', null, [
                        'label'    => 'businessReviewsList',
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_review.html.twig',
                    ])
                ->end()
            ->end()
        ;

        // Report tabs
        if ($this->getSubject()->getId()) {
            $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_THIS_WEEK);

            $showMapper
                ->tab('Interactions Report')
                    ->with('Interactions Report')
                        ->add('interactionReportFilters', null, [
                            'label'     => 'Interaction Filters',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_controls.html.twig',
                            'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'dateEnd'   => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'format'    => self::DATE_PICKER_REPORT_FORMAT,
                        ])
                        ->add('interactionReport', null, [
                            'label'    => 'Interaction Report',
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('interactionExport', null, [
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_export_buttons.html.twig',
                        ])
                    ->end()
                ->end()
                ->tab('Keywords Report')
                    ->with('Keywords Report')
                        ->add('keywordReportLimit', null, [
                            'label'     => 'Keyword Limit',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_limit.html.twig',
                            'choices'   => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
                            'data'      => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
                        ])
                        ->add('keywordReportFilters', null, [
                            'label'     => 'Keyword Filters',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_controls.html.twig',
                            'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'dateEnd'   => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'format'    => self::DATE_PICKER_REPORT_FORMAT,
                        ])
                        ->add('keywordReport', null, [
                            'label'     => 'Keyword Report',
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('keywordsExport', null, [
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_export_buttons.html.twig',
                        ])
                    ->end()
                ->end()
            ;

            if ($this->getSubject()->getDcOrderId()) {
                $showReportExportButtons = 'DomainBusinessBundle:Admin:BusinessProfile/report_export_buttons.html.twig';

                $showMapper
                    ->tab('Ad Usage Report')
                        ->with('Ad Usage Report')
                            ->add('adUsageReportFilters', null, [
                                'label'     => 'Ad Usage Filters',
                                'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_controls.html.twig',
                                'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
                                'dateEnd'   => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
                                'format'    => self::DATE_PICKER_REPORT_FORMAT,
                            ])
                            ->add('adUsageReport', null, [
                                'label'     => 'Ad Usage Report',
                                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                            ])
                            ->add('adUsageExport', null, [
                                'template' => $showReportExportButtons,
                            ])
                        ->end()
                    ->end()
                ;
            }
        }
    }

    /**
     * @param string $name
     * @param string $template
     */
    public function setTemplate($name, $template)
    {
        $this->templates['edit'] = 'DomainBusinessBundle:Admin:edit.html.twig';
        $this->templates['show'] = 'DomainBusinessBundle:Admin:show.html.twig';
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

        // at least 1 category is required
        if ($object->getCategories()->isEmpty()) {
            $errorElement->with('categories')
                ->addViolation($this->getTranslator()->trans('business_profile.category.required'))
                ->end()
            ;
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

    /**
     * @param BusinessProfile $entity
     */
    public function prePersist($entity)
    {
        $entity = $this->preSave($entity);
    }

    /**
     * @param BusinessProfile $entity
     */
    public function preUpdate($entity)
    {
        $this->preSave($entity);
    }

    /**
     * @param BusinessProfile $entity
     *
     * @return BusinessProfile
     */
    private function preSave($entity)
    {
        $entity = $this->handleTranslationBlock($entity);
        $entity = $this->handleSeoBlockUpdate($entity);

        return $entity;
    }

    /**
     * @param BusinessProfile $business
     *
     * @return BusinessProfile
     */
    private function handleSeoBlockUpdate(BusinessProfile $business)
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        return LocaleHelper::handleSeoBlockUpdate($business, $container);
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
            ->add('restore')
        ;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return BusinessProfile::getExportFormats();
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $exportFields['ID']         = 'id';
        $exportFields['Name']       = 'name';
        $exportFields['Slug']       = 'slug';
        $exportFields['hasVideo']   = 'hasVideo';
        $exportFields['hasMedia']   = 'hasMedia';
        $exportFields['areas']      = 'exportAreas';
        $exportFields['categories'] = 'exportCategories';
        $exportFields['phones']     = 'exportPhones';

        $exportFields['subscriptionPlan']       = 'exportSubscriptionPlan';
        $exportFields['subscriptionStartDate']  = 'exportSubscriptionStartDate';
        $exportFields['subscriptionEndDate']    = 'exportSubscriptionEndDate';

        $exportFields['updatedDate']        = 'updatedAt';
        $exportFields['updatedByUserId']    = 'updatedUser.id';
        $exportFields['updatedByUser']      = 'updatedUser.fullName';

        $exportFields['createdDate']        = 'createdAt';
        $exportFields['createdByUserId']    = 'createdUser.id';
        $exportFields['createdByUser']      = 'createdUser.fullName';

        $exportFields['userId']     = 'user.id';
        $exportFields['userName']   = 'user.fullName';
        $exportFields['userAccountUpdateDate']   = 'user.updatedAt';
        $exportFields['userAccountCreationDate'] = 'user.createdAt';

        return $exportFields;
    }

    /**
     * @param BusinessProfile $parent
     *
     * @return BusinessProfile
     */
    protected function cloneParentEntity(BusinessProfile $parent)
    {
        $entity = clone $parent;

        $entity->setSlug(null);

        $subscriptions = $entity->getSubscriptions();

        foreach ($subscriptions as $subscription) {
            $entity->removeSubscription($subscription);

            if (!$subscription->isExpired() and
                in_array($subscription->getStatus(), Subscription::getActualStatuses())
            ) {
                $cloneSubscription = clone $subscription;

                $entity->addSubscription($cloneSubscription);
            }
        }

        $translations = $parent->getTranslations();

        foreach ($translations as $translation) {
            $cloneTranslation = clone $translation;

            $cloneTranslation->setObject($entity);

            $entity->addTranslation($cloneTranslation);
        }

        return $entity;
    }

    /**
     * @param FormMapper        $formMapper
     * @param BusinessProfile   $businessProfile
     * @param string            $locale
     *
     * @return BusinessProfile
     */
    private function addTranslationBlock(FormMapper $formMapper, BusinessProfile $businessProfile, $locale)
    {
        $localePostfix = LocaleHelper::getLangPostfix($locale);

        $formMapper
            ->add('slogan' . $localePostfix, TextType::class, [
                'label'    => 'Slogan',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN, $locale),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('description' . $localePostfix, CKEditorType::class, [
                'label'    => 'Description',
                'required' => false,
                'mapped'   => false,
                'config_name' => 'extended_text',
                'config'      => [
                    'width'  => '100%',
                ],
                'attr' => [
                    'class' => 'text-editor',
                ],
                'data'     => $businessProfile->getTranslation(
                    BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,
                    $locale
                ),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('product' . $localePostfix, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                    'class' => 'vertical-resize',
                ],
                'label'    => 'Products',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(
                    BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT,
                    $locale
                ),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('brands' . $localePostfix, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                    'class' => 'vertical-resize',
                ],
                'label'    => 'Brands',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS, $locale),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS_LENGTH,
                        ]
                    )
                ],
            ])
        ;
    }

    /**
     * @param BusinessProfile   $entity
     *
     * @return BusinessProfile
     */
    private function handleTranslationBlock(BusinessProfile $entity)
    {
        $fields = BusinessProfile::getTranslatableFields();

        foreach ($fields as $field) {
            LocaleHelper::handleTranslations($entity, $field, $this->getRequest()->request->all()[$this->getUniqid()]);
        }

        return $entity;
    }
}
