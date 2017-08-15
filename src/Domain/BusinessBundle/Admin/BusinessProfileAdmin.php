<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Util\DatesUtil;
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
     * @var array
     */
    protected $translations = [];

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
            ->add('state')
            ->add('country')
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
            ->add('state')
            ->add('country')
            ->add('catalogLocality', null, [
                'sortable' => true,
                'sort_field_mapping'=> ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'catalogLocality']]
            ])
            ->add('phones')
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

        $businessProfile->setLocale(strtolower(BusinessProfile::TRANSLATION_LANG_EN));

        $subscriptionPlan = $businessProfile->getSubscription() ?
            $businessProfile->getSubscription()->getSubscriptionPlan() : new SubscriptionPlan();

        // define group zoning
        $formMapper
            ->tab('Profile', ['class' => 'col-md-12',])
                ->with('English', ['class' => 'col-md-6',])->end()
                ->with('Spanish', ['class' => 'col-md-6',])->end()
                ->with('Main', ['class' => 'col-md-12',])->end()
                ->with('Social Networks', ['class' => 'col-md-12',])->end()
                ->with('Address', ['class' => 'col-md-4',])->end()
                ->with('Map', ['class' => 'col-md-8',])->end()
                ->with('Categories', ['class' => 'col-md-6',])->end()
            ->end()
        ;

        if ($businessProfile->getId() and
            $subscriptionPlan->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM
        ) {
            $formMapper->tab('Profile')->with('SuperVM')->end()->end();
        }

        if ($businessProfile->getId() and $subscriptionPlan->getCode() > SubscriptionPlanInterface::CODE_FREE) {
            $formMapper->tab('Profile')->with('Keywords')->end()->end();
        }

        $formMapper
            ->tab('Profile', ['class' => 'col-md-12',])
                ->with('Gallery')->end()
            ->end()
        ;

        if ($businessProfile->getId() and
            $subscriptionPlan->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM
        ) {
            $formMapper->tab('Profile')->with('Video')->end()->end();
        }

        $formMapper
            ->tab('Profile')
                ->with('Status', array('class' => 'col-md-6'))->end()
                ->with('Subscriptions')->end()
                ->with('Coupons', array('class' => 'col-md-6'))->end()
                ->with('Discount', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Reviews', array('class' => 'col-md-6'))
                ->with('User Reviews')->end()
            ->end()
        ;

        $oxaConfig = $this->getConfigurationPool()->getContainer()->get('oxa_config');

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
            ->tab('Profile')
            ->with('English')
        ;

        $this->addTranslationBlock($formMapper, $businessProfile, BusinessProfile::TRANSLATION_LANG_EN);

        $formMapper->end()->end();

        $formMapper
            ->tab('Profile')
            ->with('Spanish')
        ;

        $this->addTranslationBlock($formMapper, $businessProfile, BusinessProfile::TRANSLATION_LANG_ES);

        $formMapper->end()->end();

        $formMapper
            ->tab('Profile')
                ->with('Main')
                    ->add('user', 'sonata_type_model', [
                        'required' => false,
                        'btn_delete' => false,
                        'btn_add' => false,
                        'query' => $query,
                    ])
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
                    ->add('slug', null, ['read_only' => true, 'required' => false])
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

        $formMapper
            ->tab('Profile')
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
                    ->add('hideMap')
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
                ->with('Categories')
                    ->add('categories', null, [
                        'multiple' => true,
                        'required' => true,
                        'query_builder' => function (\Domain\BusinessBundle\Repository\CategoryRepository $rep) {
                            return $rep->getAvailableCategoriesQb();
                        },
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
                    ->add('paymentMethods', null, [
                        'multiple' => true,
                        'expanded' => true,
                    ])
                ->end()
                ->with('Gallery')
                    ->add(
                        'logo', 'sonata_type_model_list',
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
                            ]
                        ]
                    )
                    ->add('images', 'sonata_type_collection', [
                        'by_reference' => false,
                        'required' => false,
                        'mapped' => true,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'link_parameters' => [
                            'context' => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES,
                            'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                        ]
                    ])
                    ->add('panoramaId')
                ->end()
            ->end()
        ;

        if ($businessProfile->getId() and
            $subscriptionPlan->getCode() >= SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM
        ) {
            $formMapper
                ->tab('Profile')
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

            $formMapper
                ->tab('Profile')
                    ->with('Video')
                        ->add('video', 'sonata_type_model_list', [
                            'required' => false,
                        ])
                    ->end()
                ->end()
            ;
        }

        if ($businessProfile->getId() and $subscriptionPlan->getCode() > SubscriptionPlanInterface::CODE_FREE) {
            $formMapper
                ->tab('Profile')
                ->with('Keywords')
                    ->add(
                        'keywords',
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

        $formMapper
            ->tab('Profile')
                ->with('Status')
                    ->add('isActive')
                    ->add('isDeleted', null, [
                        'label' => 'Scheduled for deletion',
                        'required' => false,
                        'disabled' => true,
                    ])
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
                ->with('DoubleClick')
                    ->add(
                        'dcOrderId',
                        null,
                        [
                            'label' => 'DC Order Id for Ad Usage Report',
                        ]
                    )
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

        if ($this->getSubject()->getId()) {
            $formMapper
                ->tab('Interactions Report')
                    ->with('Interactions Report')
                        ->add('interactionDateStart', 'sonata_type_date_picker', [
                            'mapped'    => false,
                            'required'  => false,
                            'format'    => self::DATE_PICKER_FORMAT,
                            'data'      => DatesUtil::getThisWeekStart(),
                        ])
                        ->add('interactionDateEnd', 'sonata_type_date_picker', [
                            'mapped'    => false,
                            'required'  => false,
                            'format'    => self::DATE_PICKER_FORMAT,
                            'data'      => DatesUtil::getThisWeekEnd(),
                        ])
                    ->end()
                ->end()
                ->tab('Keywords Report')
                    ->with('Keywords Report')
                        ->add('keywordDateStart', 'sonata_type_date_picker', [
                            'mapped'    => false,
                            'required'  => false,
                            'format'    => self::DATE_PICKER_FORMAT,
                            'data'      => DatesUtil::getThisWeekStart(),
                        ])
                        ->add('keywordDateEnd', 'sonata_type_date_picker', [
                            'mapped'    => false,
                            'required'  => false,
                            'format'    => self::DATE_PICKER_FORMAT,
                            'data'      => DatesUtil::getThisWeekEnd(),
                        ])
                        ->add('keywordLimit', ChoiceType::class, [
                            'mapped'    => false,
                            'choices'   => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
                            'data'      => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
                        ])
                    ->end()
                ->end()
            ;

            if ($this->getSubject()->getDcOrderId()) {
                $formMapper
                    ->tab('Ad Usage Report')
                        ->with('Ad Usage Report')
                            ->add('adUsageDateStart', 'sonata_type_date_picker', [
                                'mapped'    => false,
                                'required'  => false,
                                'format'    => self::DATE_PICKER_FORMAT,
                                'data'      => DatesUtil::getThisWeekStart(),
                            ])
                            ->add('adUsageDateEnd', 'sonata_type_date_picker', [
                                'mapped'    => false,
                                'required'  => false,
                                'format'    => self::DATE_PICKER_FORMAT,
                                'data'      => DatesUtil::getThisWeekEnd(),
                            ])
                        ->end()
                    ->end()
                ;
            }
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->tab('Profile', ['class' => 'col-md-12',])
                ->with('Translatable')
                    ->add('name')
                    ->add('slogan')
                    ->add('description', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_description.html.twig',
                    ])
                    ->add('product')
                    ->add('brands')
                    ->add('workingHours', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours.html.twig',
                    ])
                ->end()
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
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours_collection.html.twig',
                    ])
                    ->add('phones', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
                ->with('Social Networks')
                    ->add('twitterURL')
                    ->add('facebookURL')
                    ->add('googleURL')
                    ->add('youtubeURL')
                    ->add('instagramURL')
                    ->add('tripAdvisorURL')
                ->end()
                ->with('Address')
                    ->add('country', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                    ])
                    ->add('state')
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
                    ->add('paymentMethods', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                    ])
                ->end()
                ->with('SuperVM')
                    ->add('extraSearches', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
                ->with('Keywords')
                    ->add('keywords', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
                ->with('Gallery')
                    ->add('logo', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_many_to_one.html.twig',
                    ])
                    ->add('background', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_many_to_one.html.twig',
                    ])
                    ->add('images', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_image_orm_one_to_many.html.twig',
                    ])
                    ->add('panoramaId', null, [
                        'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_panorama.html.twig',
                    ])
                ->end()
                ->with('Subscription')
                    ->add('subscription')
                    ->add('subscriptions', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many.html.twig',
                    ])
                ->end()
                ->with('Status')
                    ->add('isActive')
                    ->add('registrationDate')
                    ->add('isDeleted', null, [
                        'label' => 'Scheduled for deletion',
                    ])
                    ->add('updatedAt')
                    ->add('updatedUser', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                    ])
                    ->add('createdAt')
                    ->add('createdUser', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                    ])
                ->end()
                ->with('Coupons', ['class' => 'col-md-6',])
                    ->add('coupons', null, [
                        'template' => 'OxaSonataAdminBundle:ShowFields:show_coupon_orm_one_to_many.html.twig',
                    ])
                ->end()
                ->with('Discount', ['class' => 'col-md-6',])
                    ->add('discount')
                ->end()
                ->with('DoubleClick')
                    ->add('dcOrderId', null, [
                        'label' => 'DC Order Id for Ad Usage Report',
                    ])
                ->end()
            ->end()
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
                                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_export_buttons.html.twig',
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

        $fieldNameEn = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . BusinessProfile::TRANSLATION_LANG_EN;
        $fieldNameEs = BusinessProfile::BUSINESS_PROFILE_FIELD_NAME . BusinessProfile::TRANSLATION_LANG_ES;

        $formData = $this->getRequest()->request->all()[$this->getUniqid()];

        if (!trim($formData[$fieldNameEn]) and !trim($formData[$fieldNameEs])) {
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

            if (!DayOfWeekModel::validateWorkingHoursTimeBlank($object->getCollectionWorkingHours())) {
                $errorElement->with('collectionWorkingHours')
                    ->addViolation($this->getTranslator()->trans(
                        'form.collectionWorkingHours.blank',
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
        $this->preSave($entity);
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
     */
    public function postPersist($entity)
    {
        parent::postPersist($entity);
        // workaround for translation callback
        $entity->setLocale(strtolower(BusinessProfile::TRANSLATION_LANG_EN));
        $entity = $this->handleEntityPostPersist($entity);

        // workaround for spanish slug
        $entity->setLocale(strtolower(BusinessProfile::TRANSLATION_LANG_ES));
        $this->handleTranslationPostUpdate($entity);
    }

    /**
     * @param BusinessProfile $entity
     */
    public function postUpdate($entity)
    {
        parent::postUpdate($entity);
        // workaround for translation callback
        $this->handleTranslationPostUpdate($entity);
    }

    /**
     * @param BusinessProfile $entity
     */
    private function preSave($entity)
    {
        $entity = $this->handleTranslationBlock($entity);
        $this->handleSeoBlockUpdate($entity);
    }

    /**
     * @param BusinessProfile $entity
     *
     * @return BusinessProfile
     */
    private function handleSeoBlockUpdate(BusinessProfile $entity)
    {
        /** @var ContainerInterface $container */
        $container    = $this->getConfigurationPool()->getContainer();

        $seoTitleEn = BusinessProfileUtil::seoTitleBuilder(
            $entity,
            $container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoTitleEs = BusinessProfileUtil::seoTitleBuilder(
            $entity,
            $container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $seoDescriptionEn = BusinessProfileUtil::seoDescriptionBuilder(
            $entity,
            $container,
            BusinessProfile::TRANSLATION_LANG_EN
        );

        $seoDescriptionEs = BusinessProfileUtil::seoDescriptionBuilder(
            $entity,
            $container,
            BusinessProfile::TRANSLATION_LANG_ES
        );

        $this->handleTranslationSet(
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            [
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_EN => $seoTitleEn,
                BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_TITLE . BusinessProfile::TRANSLATION_LANG_ES => $seoTitleEs,
            ]
        );

        $seoDescKeyEn = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_EN;
        $seoDescKeyEs = BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION . BusinessProfile::TRANSLATION_LANG_ES;

        $this->handleTranslationSet(
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
            [
                $seoDescKeyEn => $seoDescriptionEn,
                $seoDescKeyEs => $seoDescriptionEs,
            ]
        );

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
        $exportFields['Name']       = 'nameEn';
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
        $formMapper
            ->add('name' . $locale, TextType::class, [
                'label'    => 'Name',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_NAME, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_NAME_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('slogan' . $locale, TextType::class, [
                'label'    => 'Slogan',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('description' . $locale, CKEditorType::class, [
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
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('product' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                    'class' => 'vertical-resize',
                ],
                'label'    => 'Products',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('brands' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                    'class' => 'vertical-resize',
                ],
                'label'    => 'Brands',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_BRANDS_LENGTH,
                        ]
                    )
                ],
            ])
            ->add('workingHours' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                    'class' => 'vertical-resize',
                ],
                'label'    => 'Working hours',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation(BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS, strtolower($locale)),
                'constraints' => [
                    new Length(
                        [
                            'max' => BusinessProfile::BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH,
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
            $this->handleTranslationSet($entity, $field, $this->getRequest()->request->all()[$this->getUniqid()]);
        }

        return $entity;
    }

    /**
     * @param BusinessProfile   $entity
     * @param string            $property
     * @param array             $data
     *
     * @return BusinessProfile
     */
    private function handleTranslationSet(BusinessProfile $entity, $property, $data)
    {
        $propertyEn = $property . BusinessProfile::TRANSLATION_LANG_EN;
        $propertyEs = $property . BusinessProfile::TRANSLATION_LANG_ES;

        $dataEn = false;
        $dataEs = false;

        if (!empty($data[$propertyEn])) {
            $dataEn = trim($data[$propertyEn]);
        }

        if (!empty($data[$propertyEs])) {
            $dataEs = trim($data[$propertyEs]);
        }

        if (property_exists($entity, $property)) {
            if ($dataEs) {
                if ($entity->getId() and $dataEn) {
                    $entity->{'set' . $property}($dataEn);
                } else {
                    $entity->{'set' . $property}($dataEs);
                }

                if (property_exists($entity, $propertyEs)) {
                    $entity->{'set' . $propertyEs}($dataEs);
                }

                $this->addBusinessTranslation($entity, $property, $dataEs, BusinessProfile::TRANSLATION_LANG_ES);
            } elseif ($dataEn) {
                if (!$entity->{'get' . $property}()) {
                    $entity->{'set' . $property}($dataEn);
                }
            }

            if ($dataEn) {
                $this->addBusinessTranslation($entity, $property, $dataEn, BusinessProfile::TRANSLATION_LANG_EN);

                if (property_exists($entity, $propertyEn)) {
                    $entity->{'set' . $propertyEn}($dataEn);
                }
            } else {
                $this->prepareTranslationDelete(BusinessProfile::TRANSLATION_LANG_EN, $property);
            }

            if (!$dataEs) {
                $this->prepareTranslationDelete(BusinessProfile::TRANSLATION_LANG_ES, $property);
            }
        }

        return $entity;
    }

    /**
     * @param BusinessProfile   $entity
     * @param string            $property
     * @param array             $data
     * @param string            $locale
     *
     * @return BusinessProfile
     */
    private function addBusinessTranslation(BusinessProfile $entity, $property, $data, $locale)
    {
        if ($entity->getId()) {
            $translation = $entity->getTranslationItem(
                $property,
                mb_strtolower($locale)
            );

            if ($translation) {
                $translation->setContent($data);
            } else {
                $translation = new BusinessProfileTranslation(
                    mb_strtolower($locale),
                    $property,
                    $data
                );

                $entity->addTranslation($translation);
            }
        } else {
            $translation = new BusinessProfileTranslation(
                mb_strtolower($locale),
                $property,
                $data
            );

            $entity->addTranslation($translation);
        }

        $this->translations[$property . $locale] = [
            'locale'  => $locale,
            'field'   => $property,
            'content' => $data,
        ];

        return $entity;
    }

    /**
     * @param string    $locale
     * @param string    $field
     */
    protected function prepareTranslationDelete($locale, $field)
    {
        $this->translations[$field . $locale] = [
            'locale'  => $locale,
            'field'   => $field,
            'content' => null,
        ];
    }

    /**
     * @param BusinessProfile   $entity
     */
    protected function handleTranslationPostUpdate(BusinessProfile $entity)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');

        foreach ($this->translations as $translation) {
            $newTranslation = $entity->getTranslationItem(
                $translation['field'],
                mb_strtolower($translation['locale'])
            );

            if (!$translation['content']) {
                if (!$this->checkFieldTranslation($translation['field'])) {
                    $entity->{'set' . $translation['field']}(null);
                }

                if (property_exists($entity, $translation['field'] . $translation['locale'])) {
                    $entity->{'set' . $translation['field'] . $translation['locale']}(null);
                }

                if ($newTranslation) {
                    $em->remove($newTranslation);
                }
            } elseif ($newTranslation) {
                $newTranslation->setContent($translation['content']);
            } else {
                $translation = new BusinessProfileTranslation(
                    mb_strtolower($translation['locale']),
                    $translation['field'],
                    $translation['content']
                );

                $entity->addTranslation($translation);
                $em->persist($newTranslation);
            }
        }

        $em->flush();
    }

    /**
     * @param BusinessProfile   $entity
     *
     * @return BusinessProfile   $entity
     */
    protected function handleEntityPostPersist(BusinessProfile $entity)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');

        foreach ($this->translations as $translation) {
            if (strtolower($translation['locale']) == BusinessProfile::DEFAULT_LOCALE) {
                if (property_exists($entity, $translation['field'])) {
                    $entity->{'set' . ucfirst($translation['field'])}($translation['content']);
                }
            }
        }

        $em->flush();

        return $entity;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    protected function checkFieldTranslation($field)
    {
        if (empty($this->translations[$field . BusinessProfile::TRANSLATION_LANG_EN]['content']) and
            empty($this->translations[$field . BusinessProfile::TRANSLATION_LANG_ES]['content'])
        ) {
            return false;
        }

        return true;
    }
}
