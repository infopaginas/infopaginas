<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Repository\AreaRepository;
use Domain\BusinessBundle\Repository\BrandRepository;
use Domain\BusinessBundle\Repository\CategoryRepository;
use Domain\BusinessBundle\Repository\CountryRepository;
use Domain\BusinessBundle\Repository\LocalityRepository;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;
use Domain\BusinessBundle\Repository\TagRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class BusinessProfileFormType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sons Notebook',
                ],
                'label' => 'Name',
            ])
            ->add('website', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'http://sonsnotebook.com',
                ],
                'label' => 'Website',
                'required' => false,
            ])
            ->add('phones', CollectionType::class, [
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_type'   => TextType::class,
                'entry_options'  => [
                    'attr'  => [
                        'class' => 'form-control',
                        'placeholder' => '(787) 594-7273',
                    ],
                ],
                'label' => 'Phone number',
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Select categories',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Category',
                'label' => 'Categories',
                'multiple' => true,
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->getAvailableCategoriesQb();
                }
            ])
            ->add('areas', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Select areas',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Area',
                'label' => 'Areas',
                'multiple' => true,
                'query_builder' => function (AreaRepository $repository) {
                    return $repository->getAvailableAreasQb();
                }
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'sonsnotebook@info.com',
                ],
                'label' => 'Email',
            ])
            ->add('brands', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Organize, store, plan, prioritize',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Brand',
                'label' => 'Brands',
                'multiple' => true,
                'query_builder' => function (BrandRepository $repository) {
                    return $repository->getAvailableBrandsQb();
                },
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => preg_replace("/\r|\n/", "", 'At the time of study, all parents,
                       teachers, students we welcome ideas that foster greater productivity and end of the day,
                       produce better academic achievement'
                    ),
                    'rows' => 5,
                ],
                'label' => 'Description',
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Advertising, Cafeterias, Grooming, Restaurants',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Tag',
                'label' => 'Tags',
                'multiple' => true,
                'query_builder' => function (TagRepository $repository) {
                    return $repository->getAvailableTagsQb();
                },
                'required' => false,
            ])
            ->add('product', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => preg_replace("/\r|\n/", "", 'The SONS system currently offers the SON\'S starter
                     kit, notebooks, writing pads and labels.'
                    ),
                    'rows' => 3,
                ],
                'label' => 'Products',
                'required' => false,
            ])
            ->add('paymentMethods', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Select payment methods',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\PaymentMethod',
                'label' => 'Payment methods',
                'multiple' => true,
                'query_builder' => function (PaymentMethodRepository $repository) {
                    return $repository->getAvailablePaymentMethodsQb();
                },
                'required' => false,
            ])
            ->add('workingHours', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Mon - Fri  9 a.m. - 7 p.m. Sat  9:00 am-2:00pm',
                    'rows' => 3,
                ],
                'label' => 'Working hours',
                'required' => false,
            ])
            ->add('serviceAreasType', ChoiceType::class, [
                'choices' => array(
                    'area' => 'Area',
                    'locality' => 'Locality'
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('streetAddress', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '',
                ],
                'label' => 'Street address',
            ])
            ->add('map', GoogleMapFrontType::class, [
                'mapped' => false,
            ])
            ->add('latitude', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '66° 33′ 39″ N',
                ],
                'label' => 'Latitude',
                'required' => false,
            ])
            ->add('longitude', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '23° 27′ 30″ E',
                ],
                'label' => 'Longitude',
                'required' => false,
            ])
            ->add('country', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control',
                    'data-placeholder' => 'Select country',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Address\Country',
                'label' => 'Country',
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
                'required' => false,
            ])
            ->add('extendedAddress', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Extended address',
                'required' => false,
            ])
            ->add('crossStreet', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Cross street',
                'required' => false,
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
                    'placeholder' => 'https://twitter.com/user',
                ],
                'constraints' => [
                    new Url(),
                ],
                'label' => 'Twitter',
                'required' => false,
            ])
            ->add('facebookURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.facebook.com/user',
                ],
                'constraints' => [
                    new Url(),
                ],
                'label' => 'Facebook',
                'required' => false,
            ])
            ->add('googleURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://plus.google.com/user',
                ],
                'constraints' => [
                    new Url(),
                ],
                'label' => 'Google Plus',
                'required' => false,
            ])
            ->add('youtubeURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Url(),
                ],
                'label' => 'Youtube',
                'required' => false,
            ])
        ;

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
                case SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM:
                    $this->setupPremiumPlatinumPlanFormFields($businessProfile, $event->getForm());
                    break;
                default:
                    $this->setupFreePlanFormFields($businessProfile, $event->getForm());
            }
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
            'required' => false,
        ];

        $localitiesFieldOptions = [
            'attr' => [
                'class' => 'form-control select-control',
                'data-placeholder' => 'Select Localities',
                'multiple' => 'multiple',
            ],
            'class' => 'Domain\BusinessBundle\Entity\Locality',
            'label' => 'Localities',
            'multiple' => true,
            'query_builder' => function (LocalityRepository $repository) {
                return $repository->getAvailableLocalitiesQb();
            },
            'required' => false,
        ];

        if ($businessProfile->getServiceAreasType() === BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            $localitiesFieldOptions['attr']['disabled'] = 'disabled';
        } else {
            $milesOfMyBusinessFieldOptions['attr']['disabled'] = 'disabled';
        }

        $form->add('milesOfMyBusiness', TextType::class, $milesOfMyBusinessFieldOptions);
        $form->add('localities', EntityType::class, $localitiesFieldOptions);
    }

    private function setupPremiumPlatinumPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumGoldPlanFormFields($businessProfile, $form);

        $form->add('isSetVideo', CheckboxType::class, [
            'attr' => [
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ],
            'label' => 'yes',
            'required' => false,
            'read_only' => true,
        ]);
    }

    private function setupPremiumGoldPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumPlusPlanFormFields($businessProfile, $form);

        $form
            ->add('files', 'file', array(
                'attr' => [
                    'style' => 'display:none',
                    'accept' => 'jpg, png, gif, bmp, image/jpeg, image/pjpeg, image/png, image/gif,
                        image/bmp, image/x-windows-bmp',
                ],
                'data_class' => null,
                'mapped' => false,
                'multiple' => true,
            ))
            ->add('images', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'entry_type' => BusinessGalleryType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'allow_extra_fields' => true,
            ])
        ;
    }

    private function setupPremiumPlusPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPriorityPlanFormFields($businessProfile, $form);

        $isSloganSet = !empty($businessProfile->getSlogan());

        $isLogoSet = $businessProfile->getLogo() !== null;

        $form
            ->add('isSetLogo', CheckboxType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'label' => 'yes',
                'required' => false,
                'read_only' => true,
                'data' => $isLogoSet
            ])
            ->add('isSetSlogan', CheckboxType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'label' => 'yes',
                'required' => false,
                'read_only' => true,
                'data' => $isSloganSet,
            ])
            ->add('slogan', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Organize, store, plan, prioritize',
                ],
                'label' => 'Slogan',
                'required' => false,
            ])
        ;
    }

    private function setupPriorityPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupFreePlanFormFields($businessProfile, $form);

        $form
            ->add('isSetAd', CheckboxType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'label' => 'yes',
                'required' => false,
                'read_only' => true,
            ])
        ;
    }

    private function setupFreePlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $isMapSet = !empty($businessProfile->getLatitude()) && !empty($businessProfile->getLongitude());
        $isDescriptionSet = !empty($businessProfile->getDescription());

        $form
            ->add('isSetDescription', CheckboxType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'label' => 'yes',
                'required' => false,
                'read_only' => true,
                'data' => $isDescriptionSet,
            ])
            ->add('isSetMap', CheckboxType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'label' => 'yes',
                'required' => false,
                'read_only' => true,
                'data' => $isMapSet,
            ])
        ;
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
                    return array('Default', 'service_area_chosen');
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
