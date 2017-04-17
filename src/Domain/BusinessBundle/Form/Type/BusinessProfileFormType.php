<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\AreaRepository;
use Domain\BusinessBundle\Repository\CategoryRepository;
use Domain\BusinessBundle\Repository\CountryRepository;
use Domain\BusinessBundle\Repository\LocalityRepository;
use Domain\BusinessBundle\Repository\NeighborhoodRepository;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;
use Domain\SiteBundle\Validator\Constraints\ConstraintUrlExpanded;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\Sonata\MediaBundle\Entity\Media as SonataMedia;
use Oxa\VideoBundle\Form\Type\VideoMediaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class BusinessProfileFormType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessProfileFormType extends AbstractType
{
    protected $isUserSectionRequired = false;

    public function __construct($isUserSectionRequired = false)
    {
        $this->isUserSectionRequired = $isUserSectionRequired;
    }

    public function setCurrentUser(Session $session)
    {
        if ($session->has('_security_user')) {
            $this->isUserSectionRequired = false;
        } else {
            $this->isUserSectionRequired = true;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->isUserSectionRequired) {
            $emailConstraints = [new NotBlank()];
        } else {
            $emailConstraints = [];
        }


        $builder
            ->add('website', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'website.example.placeholder',
                ],
                'label' => 'Website',
                'required' => false,
            ])
            ->add('phones', CollectionType::class, [
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_type'   => BusinessProfilePhoneType::class,
                'label' => 'Phone number',
                'required' => false,
            ])
            ->add('collectionWorkingHours', CollectionType::class, [
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_type'   => BusinessProfileWorkingHourType::class,
                'label' => 'Working Hours',
                'required' => false,
            ])
            ->add('collectionWorkingHoursError', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'email.example.placeholder',
                ],
                'label' => 'Email',
                'constraints' => $emailConstraints,
            ])
            ->add('paymentMethods', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'Select payment methods',
                    'multiple' => true,
                ],
                'class' => 'Domain\BusinessBundle\Entity\PaymentMethod',
                'label' => 'Payment methods',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'query_builder' => function (PaymentMethodRepository $repository) {
                    return $repository->getAvailablePaymentMethodsQb();
                },
                'required' => false,
            ])
            ->add('serviceAreasType', ChoiceType::class, [
                'choices' => BusinessProfile::getServiceAreasTypes(),
                'label' => 'Service Areas',
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'choice_translation_domain' => true,
            ])
            ->add('streetAddress', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'business.add.streetAddress.placeholder',
                ],
                'label' => 'Street address',
            ])
            ->add('map', GoogleMapFrontType::class, [
                'mapped' => false,
                'label'  => 'Map',
                'label_attr' => [
                    'class' => 'title-label'
                ],
            ])
            ->add('latitude', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'latitude.example.placeholder',
                ],
                'label' => 'Latitude',
                'required' => false,
                'constraints' => [
                    new Type('float'),
                ],
            ])
            ->add('longitude', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'longitude.example.placeholder',
                ],
                'label' => 'Longitude',
                'required' => false,
                'constraints' => [
                    new Type('float'),
                ],
            ])
            ->add('country', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control',
                    'placeholder' => 'Select country',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Address\Country',
                'label' => 'Country',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'query_builder' => function (CountryRepository $repository) {
                    return $repository->getAvailableCountriesQb();
                }
            ])
            ->add('state', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'State',
                'required' => false,
            ])
            ->add('catalogLocality', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control',
                    'placeholder' => 'Select catalog locality',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Locality',
                'label' => 'Catalog Locality',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'query_builder' => function (LocalityRepository $repository) {
                    return $repository->getAvailableLocalitiesQb();
                },
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'City',
            ])
            ->add('zipCode', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Zip code',
                'required' => true,
            ])
            ->add('customAddress', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Display custom address',
                'required' => false,
            ])
            ->add('hideAddress', CheckboxType::class, [
                'label' => 'Hide Address',
                'required' => false,
            ])
            ->add('twitterURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'twitter.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'Twitter',
                'required' => false,
            ])
            ->add('facebookURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'facebook.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'Facebook',
                'required' => false,
            ])
            ->add('googleURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'plus.google.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'Google Plus',
                'required' => false,
            ])
            ->add('youtubeURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'youtube.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'Youtube',
                'required' => false,
            ])
            ->add('instagramURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'instagram.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'Instagram',
                'required' => false,
            ])
            ->add('tripAdvisorURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'tripAdvisor.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                ],
                'label' => 'TripAdvisor',
                'required' => false,
            ])
        ;

        if ($this->isUserSectionRequired) {
            $builder
                ->add('firstname', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Catalá',
                    ],
                    'label' => 'First Name',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
                ->add('lastname', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Joyeros',
                    ],
                    'label' => 'Last Name',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
            ;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var BusinessProfile $businessProfile */
            $businessProfile = $event->getData() !== null ? $event->getData() : new BusinessProfile();

            $subscription = (new SubscriptionPlan())->setCode(SubscriptionPlanInterface::CODE_FREE);

            if ($businessProfile !== null && $businessProfile->getSubscriptionPlan() !== null) {
                $subscription = $businessProfile->getSubscriptionPlan();
            }

            $this->setupServiceAreasFormFields($businessProfile, $event->getForm());

            switch ($subscription->getCode()) {
                case SubscriptionPlanInterface::CODE_PRIORITY:
                    $this->setupPriorityPlanFormFields($businessProfile, $event->getForm());
                    break;
                case SubscriptionPlanInterface::CODE_PREMIUM_PLUS:
                    $this->setupPremiumPlusPlanFormFields($businessProfile, $event->getForm());
                    break;
                case SubscriptionPlanInterface::CODE_PREMIUM_GOLD:
                    $this->setupPremiumGoldPlanFormFields($businessProfile, $event->getForm());
                    break;
                case SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM:
                case SubscriptionPlanInterface::CODE_SUPER_VM:
                    $this->setupPremiumPlatinumPlanFormFields($businessProfile, $event->getForm());
                    break;
                default:
                    $this->setupFreePlanFormFields($businessProfile, $event->getForm());
            }

            $this->setupCategories($businessProfile, $event->getForm());

            $this->addTranslationBlock($event->getForm(), $businessProfile, BusinessProfile::TRANSLATION_LANG_EN);
            $this->addTranslationBlock($event->getForm(), $businessProfile, BusinessProfile::TRANSLATION_LANG_ES);
        });
    }

    private function setupServiceAreasFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $milesOfMyBusinessFieldOptions = [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => '100',
            ],
            'label' => 'Within miles of my business',
            'required' => true,
        ];

        $areasFieldOptions = [
            'attr' => [
                'class' => 'form-control selectize-control select-multiple',
                'placeholder' => 'Select areas',
                'multiple' => 'multiple',
            ],
            'class' => 'Domain\BusinessBundle\Entity\Area',
            'label' => 'Areas',
            'label_attr' => [
                'class' => 'title-label',
            ],
            'required' => true,
            'multiple' => true,
            'query_builder' => function (AreaRepository $repository) {
                return $repository->getAvailableAreasQb();
            },
        ];

        $localitiesFieldOptions = [
            'attr' => [
                'class' => 'form-control selectize-control',
                'placeholder' => 'Select Localities',
                'multiple' => true,
            ],
            'class' => 'Domain\BusinessBundle\Entity\Locality',
            'label' => 'Localities',
            'label_attr' => [
                'class' => 'title-label'
            ],
            'multiple'      => true,
            'required'      => true,
            'query_builder' => function (LocalityRepository $repository) {
                return $repository->getAvailableLocalitiesQb();
            },
        ];

        $neighborhoodsFieldOptions = [
            'attr' => [
                'class' => 'form-control selectize-control',
                'placeholder' => 'Select Neighborhoods',
                'multiple' => true,
            ],
            'class' => 'Domain\BusinessBundle\Entity\Neighborhood',
            'label' => 'Neighborhoods',
            'label_attr' => [
                'class' => 'title-label'
            ],
            'multiple' => true,
            'query_builder' => function (NeighborhoodRepository $repository) {
                return $repository->getAvailableNeighborhoodsQb();
            },
            'required' => false,
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

        $form->add('milesOfMyBusiness', TextType::class, $milesOfMyBusinessFieldOptions);
        $form->add('areas', EntityType::class, $areasFieldOptions);
        $form->add('localities', EntityType::class, $localitiesFieldOptions);
        $form->add('neighborhoods', EntityType::class, $neighborhoodsFieldOptions);
    }

    private function setupPremiumPlatinumPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumGoldPlanFormFields($businessProfile, $form);

        $form->add('videoFile', FileType::class, [
            'attr' => [
                'style' => 'display:none',
                'accept' => 'mov, avi, mp4, wmv, flv, video/quicktime, application/x-troff-msvideo, video/avi,
                    video/msvideo, video/x-msvideo, video/mp4, video/x-ms-wmv, video/x-flv',
            ],
            'data_class' => null,
            'mapped' => false,
        ]);

        $form->add('video', VideoMediaType::class, [
            'data_class' => 'Oxa\VideoBundle\Entity\VideoMedia',
            'by_reference' => false,
        ]);
    }

    private function setupPremiumGoldPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumPlusPlanFormFields($businessProfile, $form);
    }

    private function setupPremiumPlusPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPriorityPlanFormFields($businessProfile, $form);

        $form
            ->add(
                'files',
                'file',
                [
                    'attr' => [
                        'style' => 'display:none',
                        'accept' => 'jpg, png, gif, bmp, image/jpeg, image/pjpeg, image/png, image/gif,
                            image/bmp, image/x-windows-bmp',
                    ],
                    'data_class' => null,
                    'mapped' => false,
                    'multiple' => true,
                ]
            )
            ->add('images', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'entry_type' => BusinessGalleryType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'allow_extra_fields' => true,
            ])
            ->add('logo', BusinessLogoType::class, [
                'data_class' => 'Oxa\Sonata\MediaBundle\Entity\Media',
                'by_reference' => false,
            ])
            ->add('background', BusinessBackgroundType::class, [
                'data_class' => 'Oxa\Sonata\MediaBundle\Entity\Media',
                'by_reference' => false,
            ])
        ;

        $this->addSloganTranslationBlock($form, $businessProfile, BusinessProfile::TRANSLATION_LANG_EN);
        $this->addSloganTranslationBlock($form, $businessProfile, BusinessProfile::TRANSLATION_LANG_ES);
    }

    private function setupPriorityPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupFreePlanFormFields($businessProfile, $form);
    }

    private function setupFreePlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {

    }

    private function setupCategories(BusinessProfile $businessProfile, FormInterface $form)
    {
        $category   = $businessProfile->getCategory();
        $categories2 = $businessProfile->getSubcategories(Category::CATEGORY_LEVEL_2);
        $categories3 = $businessProfile->getSubcategories(Category::CATEGORY_LEVEL_3);

        $form
            ->add('categories', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'Select category',
                    'multiple' => false,
                ],
                'class' => 'Domain\BusinessBundle\Entity\Category',
                'label' => 'user.business.type_category_1',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => false,
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->getAvailableParentCategoriesQb();
                },
                'data' => $category,
                'mapped' => false,
                'validation_groups' => ['userBusinessProfile'],
            ])
            ->add('categories2', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'Select subcategories',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Category',
                'label' => 'user.business.type_category_2',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->getAvailableChildCategoriesQb(Category::CATEGORY_LEVEL_2);
                },
                'data' => $categories2,
                'mapped' => false,
                'required' => false,
            ])
            ->add('categories3', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'Select subcategories',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Category',
                'label' => 'user.business.type_category_3',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->getAvailableChildCategoriesQb(Category::CATEGORY_LEVEL_3);
                },
                'data' => $categories3,
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    private function addTranslationBlock(FormInterface $form, BusinessProfile $businessProfile, $locale)
    {
        $form
            ->add('name' . $locale, TextType::class, [
                'label'    => 'Name',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('name', strtolower($locale)),
            ])
            ->add('description' . $locale, CKEditorType::class, [
                'label'    => 'Description',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('description', strtolower($locale)),
                'config_name' => 'extended_text',
                'config'      => [
                    'width'  => '100%',
                ],
                'attr' => [
                    'class' => 'text-editor',
                ],
            ])
            ->add('product' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                ],
                'label'    => 'Products',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('product', strtolower($locale)),
            ])
            ->add('brands' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                ],
                'label'    => 'Brands',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('brands', strtolower($locale)),
            ])
            ->add('workingHours' . $locale, TextareaType::class, [
                'attr' => [
                    'rows' => 3,
                ],
                'label'    => 'Working hours',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('workingHours', strtolower($locale)),
            ])
        ;
    }

    private function addSloganTranslationBlock(FormInterface $form, BusinessProfile $businessProfile, $locale)
    {
        $form->add('slogan' . $locale, TextType::class, [
            'label' => 'Slogan',
            'required' => false,
            'mapped'   => false,
            'data'     => $businessProfile->getTranslation('slogan', strtolower($locale)),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => 'Domain\BusinessBundle\Entity\BusinessProfile',
            'validation_groups' => function (FormInterface $form) {
                /** @var BusinessProfile $profile */
                $profile = $form->getData();

                if (BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE == $profile->getServiceAreasType()) {
                    return ['Default', 'service_area_chosen'];
                } elseif (BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE == $profile->getServiceAreasType()) {
                    return ['Default', 'service_locality_chosen'];
                } else {
                    return ['Default'];
                }
            },
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_profile_form_type';
    }
}
