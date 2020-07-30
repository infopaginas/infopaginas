<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Repository\LocalityRepository;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfilePhoneTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Oxa\Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $builder
            ->add('name', TextType::class, [
                'label'    => 'Name of Business',
                'required' => true,
            ])
            ->add('websiteItem', CustomUrlType::class, [
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
                'required' => true,
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
                'required' => true,
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
                'label' => 'Business Email',
                'constraints' =>  [
                    new NotBlank(),
                ],
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

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $this->addTranslationBlock($event->getForm(), $businessProfile, $locale);
            }
        });
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
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class'         => 'Domain\BusinessBundle\Entity\BusinessProfile',
        ]);
    }
}
