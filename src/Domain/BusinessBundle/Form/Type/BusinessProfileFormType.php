<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\AreaRepository;
use Domain\BusinessBundle\Repository\LocalityRepository;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfilePhoneTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\VideoBundle\Form\Type\VideoMediaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
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

    /**
     * BusinessProfileFormType constructor.
     *
     * @param bool $isUserSectionRequired
     */
    public function __construct($isUserSectionRequired = false)
    {
        $this->isUserSectionRequired = $isUserSectionRequired;
    }

    /**
     * @param Session $session
     */
    public function setCurrentUser(Session $session)
    {
        if ($session->has('_security_user')) {
            $this->isUserSectionRequired = false;
        } else {
            $this->isUserSectionRequired = true;
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->isUserSectionRequired) {
            $emailConstraints = [new NotBlank()];
        } else {
            $emailConstraints = [];
        }

        $builder
            ->add('name', TextType::class, [
                'label'    => 'Name',
                'required' => true,
            ])
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
            ->add(BusinessProfilePhoneTypeValidator::ERROR_BLOCK_PATH, TextType::class, [
                'mapped'   => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('collectionWorkingHours', CollectionType::class, [
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_type'   => BusinessProfileWorkingHourType::class,
                'label' => 'Working Hours',
                'required' => false,
            ])
            ->add(BusinessProfileWorkingHourTypeValidator::ERROR_BLOCK_PATH, TextType::class, [
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
                    'class' => 'title-label',
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
                    'class'       => 'form-control',
                    'placeholder' => 'business.add.streetAddress.placeholder',
                ],
                'label' => 'Street address',
            ])
            ->add('map', GoogleMapFrontType::class, [
                'mapped'     => false,
                'label'      => 'Map',
                'label_attr' => [
                    'class' => 'title-label',
                ],
            ])
            ->add('latitude', NumberType::class, [
                'attr'        => [
                    'class'       => 'form-control',
                    'placeholder' => 'latitude.example.placeholder',
                ],
                'label'       => 'Latitude',
                'required'    => false,
                'constraints' => [
                    new Type('float'),
                ],
            ])
            ->add('longitude', NumberType::class, [
                'attr'        => [
                    'class'       => 'form-control',
                    'placeholder' => 'longitude.example.placeholder',
                ],
                'label'       => 'Longitude',
                'required'    => false,
                'constraints' => [
                    new Type('float'),
                ],
            ])
            ->add('catalogLocality', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control',
                    'placeholder' => 'Select catalog locality',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Locality',
                'label' => 'Catalog Locality',
                'label_attr' => [
                    'class' => 'title-label',
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
        ;

        $this->addCategoryAutoComplete($builder);

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
                    $this->setupPremiumPlatinumPlanFormFields($businessProfile, $event->getForm());
                    break;
                default:
                    $this->setupFreePlanFormFields($businessProfile, $event->getForm());
            }

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $this->addTranslationBlock($event->getForm(), $businessProfile, $locale);
            }
        });
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     */
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

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     */
    private function setupServiceAreasFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $milesOfMyBusinessFieldOptions = [
            'attr'     => [
                'class'       => 'form-control',
                'placeholder' => '100',
            ],
            'label'    => 'Within miles of my business',
            'required' => true,
        ];

        $localitiesFieldOptions = [
            'attr'          => [
                'class'       => 'form-control selectize-control',
                'placeholder' => 'Select Localities',
                'multiple'    => true,
            ],
            'class'         => 'Domain\BusinessBundle\Entity\Locality',
            'label'         => 'Localities',
            'label_attr'    => [
                'class' => 'title-label',
            ],
            'multiple'      => true,
            'required'      => true,
            'query_builder' => function (LocalityRepository $repository) {
                return $repository->getAvailableLocalitiesQb();
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

        $form->add('milesOfMyBusiness', TextType::class, $milesOfMyBusinessFieldOptions);
        $form->add('localities', EntityType::class, $localitiesFieldOptions);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     */
    private function setupPremiumGoldPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumPlusPlanFormFields($businessProfile, $form);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     */
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
            ->add('images', CollectionType::class, [
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

        foreach (LocaleHelper::getLocaleList() as $locale => $name) {
            $this->addSloganTranslationBlock($form, $businessProfile, $locale);
        }
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     */
    private function setupPriorityPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupFreePlanFormFields($businessProfile, $form);
    }

    private function setupFreePlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {

    }

    /**
     * @param FormBuilderInterface  $builder
     */
    private function addCategoryAutoComplete($builder)
    {
        $builder
            ->add('categoryIds', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'Select categories',
                    'multiple' => true,
                ],
                'label' => 'Categories',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'mapped' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                    ]),
                ]
            ])
        ;

        $builder->get('categoryIds')->resetViewTransformers();
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     * @param string $locale
     */
    private function addTranslationBlock(FormInterface $form, BusinessProfile $businessProfile, $locale)
    {
        $localePostfix = LocaleHelper::getLangPostfix($locale);

        $form
            ->add('description' . $localePostfix, CKEditorType::class, [
                'label'    => 'Description',
                'required' => false,
                'mapped'   => false,
                'data'     => $businessProfile->getTranslation('description', $locale),
                'config_name' => 'extended_text',
                'config'      => [
                    'width'  => '100%',
                ],
                'attr' => [
                    'class' => 'text-editor',
                ],
            ]);
        ;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FormInterface $form
     * @param string $locale
     */
    private function addSloganTranslationBlock(FormInterface $form, BusinessProfile $businessProfile, $locale)
    {
        $localePostfix = LocaleHelper::getLangPostfix($locale);

        $form->add('slogan' . $localePostfix, TextType::class, [
            'label' => 'Slogan',
            'required' => false,
            'mapped'   => false,
            'data'     => $businessProfile->getTranslation('slogan', $locale),
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class'         => 'Domain\BusinessBundle\Entity\BusinessProfile',
            'validation_groups'  => function (FormInterface $form) {
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
