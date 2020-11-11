<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePopup;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Form\Handler\BusinessFormHandlerInterface;
use Domain\BusinessBundle\Form\Type\BusinessGalleryAdminType;
use Domain\BusinessBundle\Form\Type\CustomUrlType;
use Domain\BusinessBundle\Form\Type\GoogleMapType;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfilePhoneTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\ReportInterface;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\BusinessProfileIdStringFilter;
use Oxa\Sonata\AdminBundle\Filter\CaseInsensitiveStringFilter;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Form\Type\CollectionMediaType;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class BusinessProfileAdmin
 * @package Domain\BusinessBundle\Admin
 */
class BusinessProfileAdmin extends OxaAdmin
{
    public const MAX_VALIDATION_RESULT     = 5;
    public const DEFAULT_VALIDATION_GROUPS = ['Default', 'Admin'];

    private const DATE_PICKER_REPORT_FORMAT = 'YYYY-MM-DD';
    private const SONATA_FILTER_DATE_FORMAT = 'd-m-Y H:i:s';

    private const FILTER_IMPRESSIONS = 'impressions';
    private const FILTER_DIRECTIONS  = 'directions';
    private const FILTER_CALL_MOBILE = 'callsMobile';

    private const MILES_OF_MY_BUSINESS_PLACEHOLDER = 20;

    /**
     * @var bool
     */
    public $copyAvailable = true;

    /**
     * @var bool
     */
    public $emergencyCopyAvailable = true;

    /**
     * @var bool
     */
    public $allowBatchRestore = true;

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => self::DEFAULT_VALIDATION_GROUPS,
    ];

    public $imageHelpMessage = 'imageHelpMessage';

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

    public function getFormBuilder()
    {
        $this->formOptions['validation_groups'] = function (FormInterface $form) {
            $validationGroups = BusinessProfileAdmin::DEFAULT_VALIDATION_GROUPS;
            if (!$form->getData()->isEnableNotUniquePhone()) {
                $validationGroups[] = BusinessFormHandlerInterface::UNIQUE_PHONE_VALIDATION_GROUP;
            }

            return $validationGroups;
        };

        return parent::getFormBuilder();
    }

    /**
     * @return array
     */
    public function getCustomActions()
    {
        return [
            'label_show_leads' => $this->generateUrl(
                'list',
                [
                    'filter' => [
                        'subscriptions__subscriptionPlan' => [
                            'value' => SubscriptionPlan::CODE_FREE,
                        ],
                        'registrationDate'                => [
                            'value' => [
                                'start' => DatesUtil::getLastMonth()->format(self::SONATA_FILTER_DATE_FORMAT),
                            ],
                        ],
                        'tasks__type'                     => [
                            'value' => true,
                        ],
                        '_page'                           => 1,
                        '_per_page'                       => $this->getFilterParameters()['_per_page'],
                    ],
                ]
            ),
        ];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     * @return bool
     */
    public function overviewFilterQueryBuilder($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return false;
        }

        $borderValue = $this->getFilterTypeConfig($field);

        $operator = $value['value'];

        $queryBuilder->andWhere($alias . '.' . $field . ' ' . $operator . ' :borderValue')
            ->setParameter('borderValue', $borderValue);

        return true;
    }

    /**
     * @param string $action
     * @return string
     */
    private function getFilterTypeConfig($action)
    {
        $configService = $this->getConfigurationPool()
            ->getContainer()
            ->get('oxa_config');

        switch ($action) {
            case self::FILTER_DIRECTIONS:
                $searchConfig = ConfigInterface::DIRECTIONS_FILTER_VALUE;
                break;
            case self::FILTER_CALL_MOBILE:
                $searchConfig = ConfigInterface::CALLS_MOBILE_FILTER_VALUE;
                break;
            case self::FILTER_IMPRESSIONS:
                $searchConfig = ConfigInterface::IMPRESSIONS_FILTER_VALUE;
                break;
            default:
                throw new \LogicException('This code must not be reached');
                break;
        }

        return $configService->getValue($searchConfig);
    }

    /**
     * @param string $action
     * @return array
     */
    private function getOverviewFilterOptions($action)
    {
        $borderValue = $this->getFilterTypeConfig($action);

        return [
            'choices' => [
                '< ' . $borderValue  => '<',
                '>= ' . $borderValue => '>=',
            ]
        ];
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', BusinessProfileIdStringFilter::class)
            ->add('name', CaseInsensitiveStringFilter::class, [
                'show_filter' => true,
            ])
            ->add('city')
            ->add('email');

        foreach ($this->getOverviewFilters() as $overviewFilter) {
            $datagridMapper->add(
                $overviewFilter,
                CallbackFilter::class,
                [
                    'callback' => [$this, 'overviewFilterQueryBuilder'],
                ],
                ChoiceType::class,
                $this->getOverviewFilterOptions($overviewFilter)
            );
        }

        $datagridMapper->add('catalogLocality')
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
                    'label' => $this->trans('filter.label_phone', [], $this->getTranslationDomain()),
                ]
            )
            ->add('hasImages')
            ->add('enableNotUniquePhone')
            ->add('subscriptions.subscriptionPlan', null, [
                'label' => $this->trans('filter.label_subscription_plan', [], $this->getTranslationDomain()),
            ])
            ->add('registrationDate', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
            ->add('isActive')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
            ->add('categories.name', null, [
                'label' => $this->trans('filter.label_categories.en', [], $this->getTranslationDomain()),
            ])
            ->add('categories.searchTextEs', null, [
                'label' => $this->trans('filter.label_categories.es', [], $this->getTranslationDomain()),
            ])
            ->add('tasks.type', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_created_by_user', [], $this->getTranslationDomain()),
                'callback'   => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }

                    $queryBuilder->andWhere(sprintf('%s.type = :profileCreateType', $alias));
                    $queryBuilder->setParameter('profileCreateType', TaskType::TASK_PROFILE_CREATE);

                    return true;
                },
                'field_type' => CheckboxType::class,
            ])
            ->add('isDraft', null, [
                'label' => $this->trans('filter.label_is_draft', [], $this->getTranslationDomain()),
            ], null, [
                      'placeholder' => $this->trans('all', [], 'AdminReportBundle'),
                  ])
            ->add('csvImportFile')
            ->add('hasPanoramaId', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_has_panorama_id', [], $this->getTranslationDomain()),
                'callback'   => static function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder->andWhere(sprintf('%s.panoramaId IS NOT NULL', $alias));

                    return true;
                },
                'field_type' => CheckboxType::class,
            ])
            ->add('paidProfiles', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_only_paid_profiles', [], $this->getTranslationDomain()),
                'callback'   => function ($queryBuilder, $alias, $field, $value) {
                    return $this->addMinimumActiveSubscriptionToQuery(
                        $queryBuilder,
                        $alias,
                        $field,
                        $value,
                        SubscriptionPlanInterface::CODE_PRIORITY
                    );
                },
                'field_type' => CheckboxType::class,
            ])
            ->add('hasSuperVM', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_has_supervm', [], $this->getTranslationDomain()),
                'callback'   => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    /** @var QueryBuilder $queryBuilder */
                    if (isset($this->getFilterParameters()['paidProfiles']['value'])) {
                        $queryBuilder->setParameter('code', SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM);
                    } else {
                        $this->addMinimumActiveSubscriptionToQuery(
                            $queryBuilder,
                            $alias,
                            $field,
                            $value,
                            SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM
                        );
                    }

                    $queryBuilder->innerJoin(sprintf('%s.extraSearches', $alias), 'es');

                    return true;
                },
                'field_type' => CheckboxType::class,
            ])
            ->add('isShowFacebookRating')
            ->add('hasFacebookRating', CallbackFilter::class, [
                'field_type' => CheckboxType::class,
                'callback'   => static function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder->andWhere(sprintf('%s.facebookRating IS NOT NULL', $alias));

                    return true;
                },
            ])
            ->add('hasBusinessToRedirect', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_has_redirect_to', [], $this->getTranslationDomain()),
                'field_type' => CheckboxType::class,
                'callback'   => static function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder->andWhere(sprintf('%s.businessToRedirect IS NOT NULL', $alias));

                    return true;
                },
            ])
            ->add('hasBusinessRedirectFrom', CallbackFilter::class, [
                'label'      => $this->trans('filter.label_has_redirect_from', [], $this->getTranslationDomain()),
                'field_type' => CheckboxType::class,
                'callback'   => static function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder->innerJoin(sprintf('%s.redirectedBusinesses', $alias), 'rb');
                    $queryBuilder->andWhere(sprintf('rb.businessToRedirect = %s.id', $alias));

                    return true;
                },
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
            ->add('impressions')
            ->add('directions')
            ->add('callsMobile')
            ->add('callsDesktop')
            ->add('views')
            ->add('catalogLocality', null, [
                'sortable' => true,
                'sort_field_mapping'=> ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'catalogLocality']]
            ])
            ->add('phones', null, [
                'template' => 'OxaSonataAdminBundle:ListFields:list_orm_one_to_many.html.twig',
            ])
            ->add('Address', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/full_address.html.twig'
            ])
            ->add('hasImages')
            ->add('subscriptionPlan', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/list_subscription.html.twig'
            ])
            ->add('subscription', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/list_subscription_end_date.html.twig',
                'label' => 'Subscription End Date',
            ])
            ->add('isActive')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
            ->add('isDraft')
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

        if (!$businessProfile) {
            return;
        }

        $businessProfile->setLocale(LocaleHelper::DEFAULT_LOCALE);

        $subscriptionPlanCode = $businessProfile->getSubscriptionPlanCode();

        // define tabs
        $formMapper
            ->tab('Main')->end()
            ->tab('Media')->end()
            ->tab('Others')->end()
            ->tab('Legacy URLs')->end()
            ->tab('Social Networks')->end()
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

        if ($subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_GOLD && $businessProfile->getId()) {
            $formMapper->tab('Main')->with('Testimonials')->end()->end();
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
                ->with('Gallery')->end()
                ->with('Popup')->end()
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

        if ($businessProfile->getId() and $subscriptionPlanCode == SubscriptionPlanInterface::CODE_FREE) {
            $formMapper
                ->tab('Main')
                    ->with('Main')
                        ->add('businessToRedirect', ModelListType::class, [
                            'required'   => false,
                            'btn_delete' => 'delete',
                            'btn_add'    => false,
                            'model_manager' => $this->modelManager,
                            'class' => BusinessProfile::class,
                        ], [
                            'link_parameters' => [
                                'onlyPaidProfiles'  => true,
                            ]
                        ])
                    ->end()
                ->end()
            ;
        }

        // Main tab
        // Subscription Block
        $formMapper
            ->tab('Main')
                ->with('Subscription')
                    ->add(
                        'subscriptions',
                        CollectionType::class,
                        [
                            'by_reference'  => false,
                            'required'      => true,
                            'type_options' => [
                                'delete'         => true,
                                'delete_options' => [
                                    'type'         => CheckboxType::class,
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
                    ->add('name', null, [
                        'attr' => [
                            'spellcheck' => 'true',
                        ],
                    ])
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
                    ->add('websiteItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('actionUrlType', ChoiceType::class, [
                        'choices'  => array_flip(BusinessProfile::getActionUrlTypes()),
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                        'translation_domain' => 'AdminDomainBusinessBundle',
                    ])
                    ->add('actionUrlItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('email', EmailType::class, [
                        'required' => false,
                    ])
                    ->add(
                        'slug',
                        null,
                        [
                            'attr'     => [
                                'read_only' => true,
                            ],
                            'required' => false,
                        ]
                    )
                    ->add(
                        'collectionWorkingHours',
                        CollectionType::class,
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
                        CollectionType::class,
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

        if ($subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_GOLD && $businessProfile->getId()) {
            $formMapper
                ->tab('Main')
                    ->with('Testimonials')
                        ->add(
                            'testimonials',
                            CollectionType::class,
                            [
                                'by_reference'  => false,
                                'required'      => false,
                                'type_options' => [
                                    'delete'         => true,
                                    'delete_options' => [
                                        'type'         => CheckboxType::class,
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
                ->end();
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Main')
                    ->with('Main')
                        ->add('enableNotUniquePhone')
                    ->end()
                ->end()
            ;
        }

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
                        'attr'     => [
                            'spellcheck' => 'true',
                        ],
                    ])
                    ->add('catalogLocality', ModelListType::class, [
                        'required'      => true,
                        'btn_delete'    => false,
                        'btn_add'       => false,
                        'model_manager' => $this->modelManager,
                        'class' => Locality::class,
                    ])
                    ->add('zipCode', null, [
                        'required' => true
                    ])
                    ->add('streetAddress', null, [
                        'required' => true,
                        'attr'     => [
                            'spellcheck' => 'true',
                        ],
                    ])
                    ->add('customAddress', null, [
                        'attr' => [
                            'spellcheck' => 'true',
                        ],
                    ])
                    ->add('hideAddress')
                    ->add('hideMap')
                    ->add('hideGetDirectionsButton')
                ->end()
            ->end()
        ;

        // Map Block
        $oxaConfig = $this->getConfigurationPool()->getContainer()->get('oxa_config');

        $requestData = $this->getRequest()->request->get($this->getRequest()->query->get('uniqid'));
        if (!empty($requestData['latitude']) && !empty($requestData['longitude'])) {
            $latitude  = $requestData['latitude'];
            $longitude = $requestData['longitude'];
        } elseif ($this->getSubject()->getLatitude() && $this->getSubject()->getLongitude()) {
            $latitude  = $this->getSubject()->getLatitude();
            $longitude = $this->getSubject()->getLongitude();
        } else {
            $latitude  = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE);
            $longitude = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE);
        }

        $formMapper
            ->tab('Main')
                ->with('Map')
                    ->add('latitude')
                    ->add('longitude')
                    ->add('map', GoogleMapType::class, [
                        'latitude'  => $latitude,
                        'longitude' => $longitude,
                        'mapped'    => false,
                    ])
                ->end()
            ->end()
        ;

        // Category Block
        $milesOfMyBusinessFieldOptions = [
            'required' => true,
            'attr' => [
                'placeholder' => self::MILES_OF_MY_BUSINESS_PLACEHOLDER,
            ],
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

        if ($businessProfile->getId() && $subscriptionPlanCode > SubscriptionPlanInterface::CODE_FREE) {
            $maximumSelectionSize = 0;
            $serviceAreasType = BusinessProfile::getServiceAreasTypes();
        } else {
            $maximumSelectionSize = BusinessProfile::BUSINESS_PROFILE_FREE_MAX_CATEGORIES_COUNT;
            $serviceAreasType = [
                'Locality' => BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE,
            ];
        }

        $formMapper
            ->tab('Main')
                ->with('Category')
                    ->add('categories', ModelAutocompleteType::class, [
                        'class' => Category::class,
                        'property' => [
                            'name',
                            'searchTextEs',
                        ],
                        'model_manager' => $this->modelManager,
                        'maximum_selection_size' => $maximumSelectionSize,
                        'multiple' => true,
                        'required' => true,
                    ])
                    ->add('serviceAreasType', ChoiceType::class, [
                        'choices'  => $serviceAreasType,
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                    ])
        ;

        if ($subscriptionPlanCode > SubscriptionPlanInterface::CODE_FREE && $businessProfile->getId()) {
            $formMapper->add('milesOfMyBusiness', null, $milesOfMyBusinessFieldOptions);
        }

        $formMapper->add('areas', null, $areasFieldOptions)
                    ->add('localities', null, $localitiesFieldOptions)
                    ->add('neighborhoods', null, $neighborhoodsFieldOptions)
                ->end()
            ->end()
        ;

        // Super VM Block
        if ($businessProfile->getId() && $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            $formMapper
                ->tab('Main')
                    ->with('SuperVM')
                        ->add(
                            'extraSearches',
                            CollectionType::class,
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
        if ($businessProfile->getId() && $subscriptionPlanCode > SubscriptionPlanInterface::CODE_FREE) {
            $formMapper
                ->tab('Main')
                    ->with('Keywords')
                        ->add('keywordText', TextType::class, [
                            'attr' => [
                                'class' => 'selectize-control',
                            ],
                            'required' => false,
                        ])
                        ->add('relatedKeywords', TextType::class, [
                            'attr' => [
                                'class' => 'selectize-control disabled',
                                'read_only' => true,
                            ],
                            'required'  => false,
                            'disabled'  => true,
                        ])
                    ->end()
                ->end()
            ;
        }

        // Media tab
        // Social Networks Block
        $formMapper
            ->tab('Social Networks')
                ->with('Social Networks')
                    ->add('linkedInURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('twitterURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('facebookURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('googleURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('youtubeURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('instagramURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                    ->add('tripAdvisorURLItem', CustomUrlType::class, [
                        'required' => false,
                        'by_reference'  => false,
                    ])
                ->end()
            ->end()
        ;

        // Gallery Block
        $formMapper
            ->tab('Media')
                ->with('Gallery')
                    ->add('images', CollectionMediaType::class, [
                        'entry_type'         => BusinessGalleryAdminType::class,
                        'required'           => false,
                        'allow_add'          => true,
                        'allow_delete'       => true,
                        'allow_extra_fields' => true,
                        'by_reference'       => false,
                        'sonata_help'        => $this->imageHelpMessage,
                    ])
                    ->add('addGalleryImage', FileType::class, [
                        'mapped'   => false,
                        'required' => false,
                        'help'     => 'business_profile.help.gallery',
                        'multiple' => true,
                        'attr'     => [
                            'accept' => implode(',', AdminHelper::getFormImageFileAccept()),
                        ],
                    ])
                    ->add(
                        'logo',
                        ModelListType::class,
                        [
                            'required' => false,
                            'help'     => 'business_profile.help.logo',
                            'model_manager' => $this->modelManager,
                            'class' => Media::class,
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
                        ModelListType::class,
                        [
                            'required' => false,
                            'help'     => 'business_profile.help.background',
                            'model_manager' => $this->modelManager,
                            'class' => Media::class,
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

        // Popup Block
        $formMapper
            ->tab('Media')
                ->with('Popup')
                    ->add('popup', ModelListType::class, [
                        'required' => false,
                        'btn_list' => false,
                        'model_manager' => $this->modelManager,
                        'class' => BusinessProfilePopup::class,
                    ])
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
                        ->add(
                            'video',
                            ModelListType::class,
                            [
                                'required' => false,
                                'model_manager' => $this->modelManager,
                                'class' => VideoMedia::class,
                            ],
                            [
                                'link_parameters' => [
                                    'businessName' => $businessProfile->getName(),
                                ],
                            ]
                        )
                    ->end()
                ->end();
        }

        // Message from the owner block
        if ($businessProfile->getId() && $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            $videoTitle = $businessProfile->getName() . '-' . $this->trans('Message from the Owner');
            $formMapper
                ->tab('Media')
                    ->with('Owners Message')
                        ->add(
                            'ownersMessage',
                            ModelListType::class,
                            [
                                'required' => false,
                                'model_manager' => $this->modelManager,
                                'class' => VideoMedia::class,
                            ],
                            [
                                'link_parameters' => [
                                    'businessName' => $videoTitle,
                                ],
                            ]
                        )
                    ->end()
                ->end();
        }

        // Others Tab
        // Coupons Block
        $formMapper
            ->tab('Others')
                ->with('Coupons')
                    ->add(
                        'coupons',
                        CollectionType::class,
                        [
                            'by_reference'  => false,
                            'required'      => false,
                            'type_options' => [
                                'delete'         => true,
                                'delete_options' => [
                                    'type'         => CheckboxType::class,
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
                        'label' => $this->trans('business_profile.fields.dcOrderId', [], 'AdminDomainBusinessBundle'),
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
                            CollectionType::class,
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

        if ($businessProfile->getSubscriptionPlanCode() > SubscriptionPlanInterface::CODE_FREE) {
            //Social Feeds Tab
            $formMapper
                ->tab('Social Networks')
                    ->with('Social Networks Feeds')
                        ->add(
                            'mediaUrls',
                            CollectionType::class,
                            [
                                'by_reference'  => false,
                                'required'      => false,
                            ],
                            [
                                'edit'          => 'inline',
                                'delete_empty'  => false,
                                'inline'        => 'table',
                                'sortable'      => 'position',
                            ]
                        )
                    ->end()
                ->end()
            ;
        }

        if ($businessProfile->getId() and $subscriptionPlanCode >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM) {
            $formMapper
                ->tab('More Info')
                    ->with('Checkboxes')
                        ->add(
                            'checkboxCollection',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'required'     => false,
                            ],
                            [
                                'edit'         => 'inline',
                                'delete_empty' => false,
                                'inline'       => 'table',
                            ]
                        )
                    ->end()
                    ->with('Text Areas')
                        ->add(
                            'textAreaCollection',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'required'     => false,
                            ],
                            [
                                'edit'         => 'inline',
                                'delete_empty' => false,
                                'inline'       => 'table',
                            ]
                        )
                    ->end()
                    ->with('Radio Buttons')
                        ->add(
                            'radioButtonCollection',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'required'     => false,
                            ],
                            [
                                'edit'         => 'inline',
                                'delete_empty' => false,
                                'inline'       => 'table',
                            ]
                        )
                    ->end()
                    ->with('Lists')
                        ->add(
                            'listCollection',
                            CollectionType::class,
                            [
                                'by_reference' => false,
                                'required'     => false,
                            ],
                            [
                                'edit'         => 'inline',
                                'delete_empty' => false,
                                'inline'       => 'table',
                            ]
                        )
                    ->end()
                ->end()
                ->tab('Reviews')
                    ->with('Yelp')
                        ->add('yelpURL')
                        ->add('isShowYelpRating', null, [
                            'label' => 'Show',
                        ])
                    ->end()
                    ->with('Google My Business')
                        ->add('googlePlaceId', null, [
                            'help' => '<a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank">Place ID finder</a>',
                        ])
                        ->add('isShowGooglePlaceRating', null, [
                            'label' => 'Show',
                        ])
                    ->end()
                    ->with('TripAdvisor')
                        ->add('tripAdvisorUrl')
                        ->add('isShowTripAdvisorRating', null, [
                            'label' => 'Show',
                        ])
                    ->end()
                    ->with('Facebook Page')
                        ->add('facebookRating')
                        ->add('isShowFacebookRating', null, [
                            'label' => 'Show',
                        ])
                    ->end()
                ->end()
            ;
        }

        $formMapper->tab('Reports', ['attr' => ['custom_tab_name' => 'tab-reports']])->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getSubject();

        // define tabs
        $showMapper
            ->tab('Main')->end()
            ->tab('Media')->end()
            ->tab('Others')->end()
            ->tab('Legacy URLs')->end()
            ->tab('Social Networks')->end()
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
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_purified_value.html.twig',
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
                    ->add('websiteLink')
                    ->add('actionUrlType')
                    ->add('getActionLink')
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
                    ->add('hideGetDirectionsButton')
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
                        'label' => $this->trans('business_profile.fields.dcOrderId', [], 'AdminDomainBusinessBundle'),
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

        //Social Networks Tab
        $showMapper
            ->tab('Social Networks')
                ->with('Social Networks')
                    ->add('linkedInLink')
                    ->add('twitterLink')
                    ->add('facebookLink')
                    ->add('googleLink')
                    ->add('youtubeLink')
                    ->add('instagramLink')
                    ->add('tripAdvisorLink')
                ->end()
            ->end()
        ;

        if ($businessProfile->getId() &&
            $businessProfile->getSubscriptionPlanCode() > SubscriptionPlanInterface::CODE_FREE) {
            $showMapper
                ->tab('Social Networks')
                    ->with('Social Networks Feeds')
                        ->add('mediaUrls', null, [
                            'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                        ])
                    ->end()
                ->end()
            ;
        }

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
        if ($businessProfile->getId()) {
            $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_LAST_MONTH);

            $showMapper
                ->tab('Reports', ['attr' => ['custom_tab_name' => 'tab-reports']])
                    ->with('Report Filters')
                        ->add('mainReportFilters', null, [
                            'label'     => 'Filters',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/main_report_controls.html.twig',
                            'dateStart' => $dateRange->getStartDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'dateEnd'   => $dateRange->getEndDate()->format(DatesUtil::DATE_DB_FORMAT),
                            'format'    => self::DATE_PICKER_REPORT_FORMAT,
                            'periodChoices' => DatesUtil::getReportAdminDataRanges(),
                            'periodData' => DatesUtil::RANGE_LAST_MONTH,
                        ])
                        ->add('periodOption', null, [
                            'label'     => 'Period',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_choice.html.twig',
                            'choices'   => AdminHelper::getPeriodOptionValues(),
                            'data'      => AdminHelper::PERIOD_OPTION_CODE_PER_MONTH,
                        ])
                        ->add('keywordReportLimit', null, [
                            'label'     => 'Keyword Limit',
                            'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_choice.html.twig',
                            'choices'   => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
                            'data'      => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
                        ])
                    ->end()
                    ->with('Summary')
                        ->add('', null, [
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_summary.html.twig',
                        ])
                        ->add('impressions', null, [
                            'label'     => 'Impressions',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_IMPRESSION,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('views', null, [
                            'label'     => 'Views',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_VIEW,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('socialNetworks', null, [
                            'label'     => 'Social Networks',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_SOCIAL_NETWORKS,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('directions', null, [
                            'label'     => 'Directions',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_DIRECTION_BUTTON,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('callMob', null, [
                            'label'     => 'Call (mob)',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_CALL_MOB_BUTTON,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('callDesk', null, [
                            'label'     => 'Call (desktop)',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_CALL_DESK_BUTTON,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('video', null, [
                            'label'     => 'Video',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_VIDEO_WATCHED,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                        ->add('keyword', null, [
                            'label'     => 'Keywords',
                            'eventType'     => BusinessOverviewModel::TYPE_CODE_KEYWORD,
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
                        ])
                    ->end()
                    ->with('Export')
                        ->add('mainExport', null, [
                            'label'    => 'Export',
                            'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_export_buttons.html.twig',
                            'exportRoute' => 'domain_business_admin_inretaction_reports_export',
                            'exportPdf'   => ReportInterface::FORMAT_PDF,
                            'exportExcel' => ReportInterface::FORMAT_EXCEL,
                        ])
                    ->end()
                ->end()
            ;

            if ($businessProfile->getDcOrderId()) {
                $showMapper
                    ->tab('Reports')
                        ->with('Preview')
                            ->add('adUsageReport', null, [
                                'label'     => 'Ad Usage Report',
                                'eventType' => BusinessOverviewModel::TYPE_CODE_ADS,
                                'template'  => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
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
        $this->getTemplateRegistry()->setTemplate('edit', 'DomainBusinessBundle:Admin:edit.html.twig');
        $this->getTemplateRegistry()->setTemplate('show', 'DomainBusinessBundle:Admin:show.html.twig');
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     *
     * @throws \Exception
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('streetAddress')
            ->end()
            ->with('city')
            ->end()
            ->with('zipCode')
            ->end()
        ;

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

        if ($object->getActionUrlItem()->getUrl() && $object->getOwnersMessage()) {
            $errorElement
                ->with('ownersMessage')
                    ->addViolation($this->getTranslator()->trans(
                        'form.owners_message.action_url_type',
                        [],
                        $this->getTranslationDomain()
                    ))
                ->end()
            ;
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
        if ((isset($parameters['subscriptions__subscriptionPlan']) &&
             !empty($parameters['subscriptions__subscriptionPlan']['value'])) ||
            $this->getRequest()->get('onlyPaidProfiles')
        ) {
            $query->leftJoin($query->getRootAliases()[0] . '.subscriptions', 's');
            $query->leftJoin('s.subscriptionPlan', 'sp');
            $query->andWhere('s.status = :subscriptionStatus');
            $query->setParameter('subscriptionStatus', StatusInterface::STATUS_ACTIVE);

            if ($this->getRequest()->get('onlyPaidProfiles')) {
                $query->andWhere('sp.code > :subscriptionPlanCode');
                $query->setParameter('subscriptionPlanCode', SubscriptionPlanInterface::CODE_FREE);
            }
            if ((isset($parameters['subscriptions__subscriptionPlan']) &&
                 !empty($parameters['subscriptions__subscriptionPlan']['value']))
            ) {
                $query->andWhere('sp.id = :subscriptionPlanId');

                $subscriptionPlanId = $parameters['subscriptions__subscriptionPlan']['value'];
                $query->setParameter('subscriptionPlanId', $subscriptionPlanId);
            }
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
        //If you swap those 2 lines, the title will be saved half the time
        $this->preSave($entity);
        if ($entity->getIsActive()) {
            $entity->setIsDraft(false);
        }
        parent::preUpdate($entity);
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
            ->add('exportPreview');
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

        $exportFields['City']          = 'city';
        $exportFields['StreetAddress'] = 'streetAddress';
        $exportFields['ZipCode']       = 'zipCode';
        $exportFields['lat']           = 'latitude';
        $exportFields['lng']           = 'longitude';

        $exportFields['hasVideo']   = 'hasVideo';
        $exportFields['hasOwnersMessage']   = 'hasOwnersMessage';
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

        $exportFields['socialFeedUrls']    = 'exportSocialFeedUrls';
        $exportFields['socialNetworkUrls'] = 'exportSocialNetworkUrls';

        $exportFields['catalogLocality'] = 'catalogLocality.name';

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
                'attr'     => [
                    'spellcheck' => 'true',
                ],
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH,
                        ]
                    ),
                    new Regex(
                        [
                            'match'   => false,
                            'pattern' => BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
                            'message' => 'business_profile.phone_number_check_failed',
                        ]
                    ),
                ],
            ])
            ->add('description' . $localePostfix, CKEditorType::class, [
                'label'       => 'Description',
                'required'    => false,
                'mapped'      => false,
                'config_name' => 'extended_text_scayt',
                'config'      => [
                    'width'       => '100%',
                    'scayt_sLang' => LocaleHelper::getLanguageCodeForSCAYT($locale),
                ],
                'attr'        => [
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
                    ),
                    new Regex(
                        [
                            'match'   => false,
                            'pattern' => BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
                            'message' => 'business_profile.phone_number_check_failed',
                        ]
                    ),
                ],
            ])
            ->add('product' . $localePostfix, TextareaType::class, [
                'attr' => [
                    'rows'       => 3,
                    'class'      => 'vertical-resize',
                    'spellcheck' => 'true',
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
                    ),
                    new Regex(
                        [
                            'match'   => false,
                            'pattern' => BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
                            'message' => 'business_profile.phone_number_check_failed',
                        ]
                    ),
                ],
            ])
            ->add('brands' . $localePostfix, TextareaType::class, [
                'attr' => [
                    'rows'       => 3,
                    'class'      => 'vertical-resize',
                    'spellcheck' => 'true',
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
                    ),
                    new Regex(
                        [
                            'match'   => false,
                            'pattern' => BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
                            'message' => 'business_profile.phone_number_check_failed',
                        ]
                    ),
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

    /**
     * @return array
     */
    public function getOverviewFilters()
    {
        return [
            self::FILTER_IMPRESSIONS,
            self::FILTER_DIRECTIONS,
            self::FILTER_CALL_MOBILE,
        ];
    }

    private function addMinimumActiveSubscriptionToQuery($queryBuilder, $alias, $field, $value, $code): bool
    {
        if (!$value['value']) {
            return false;
        }
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder->leftJoin(
            sprintf('%s.subscriptions', $alias),
            'sub',
            Join::WITH,
            'sub.status = :subscriptionStatus'
        );
        $queryBuilder->leftJoin('sub.subscriptionPlan', 'sp');
        $queryBuilder->andWhere('sp.code >= :code');
        $queryBuilder->setParameter('code', $code);
        $queryBuilder->setParameter('subscriptionStatus', StatusInterface::STATUS_ACTIVE);

        return true;
    }
}
