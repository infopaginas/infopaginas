<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Repository\AreaRepository;
use Domain\BusinessBundle\Repository\BrandRepository;
use Domain\BusinessBundle\Repository\CategoryRepository;
use Domain\BusinessBundle\Repository\CountryRepository;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;
use Domain\BusinessBundle\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class BusinessProfileType extends AbstractType
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
            ->add('phone', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '(787) 594-7273',
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
                'query_builder' => function(CategoryRepository $repository) {
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
                'query_builder' => function(AreaRepository $repository) {
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
            ->add('slogan', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Organize, store, plan, prioritize',
                ],
                'label' => 'Slogan',
                'required' => false,
            ])
            ->add('brands', EntityType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-multiple',
                    'data-placeholder' => 'Organize, store, plan, prioritize',
                    'multiple' => 'multiple',
                ],
                'class' => 'Domain\BusinessBundle\Entity\Brand',
                'label' => 'Brands',
                'query_builder' => function(BrandRepository $repository) {
                    return $repository->getAvailableBrandsQb();
                },
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'At the time of study, all parents, teachers, students we welcome ideas that foster greater productivity and end of the day, produce better academic achievement.',
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
                'query_builder' => function(TagRepository $repository) {
                    return $repository->getAvailableTagsQb();
                },
                'required' => false,
            ])
            ->add('product', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'The SONS system currently offers the SON\'S starter kit, notebooks, writing pads and labels.',
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
                'query_builder' => function(PaymentMethodRepository $repository) {
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
            ->add('isSetDescription', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('isSetMap', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('isSetAd', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('isSetLogo', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('isSetSlogan', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('isSetVideo', CheckboxType::class, [
                'label' => 'yes',
                'required' => false,
            ])
            ->add('streetAddress', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '',
                ],
                'label' => 'Street address',
            ])
            ->add('map', GoogleMapFrontType::class, [

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
                'query_builder' => function(CountryRepository $repository) {
                    return $repository->getAvailableCountriesQb();
                }
            ])
            ->add('state', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'State',
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\BusinessBundle\Entity\BusinessProfile',
        ]);
    }

    public function getName()
    {
        return 'domain_business_bundle_business_profile_type';
    }
}
