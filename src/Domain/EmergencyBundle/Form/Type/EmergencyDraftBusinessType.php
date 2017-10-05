<?php

namespace Domain\EmergencyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Repository\PaymentMethodRepository;

/**
 * Class EmergencyDraftBusinessType
 * @package Domain\EmergencyBundle\Form\Type
 */
class EmergencyDraftBusinessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'emergency.business_draft.name',
                'required' => true,
            ])
            ->add('category', EntityType::class, [
                'empty_value' => 'emergency.business_draft.category.title.others',
                'attr' => [
                    'class' => 'form-control selectize-control',
                ],
                'class' => 'Domain\EmergencyBundle\Entity\EmergencyCategory',
                'label' => 'emergency.business_draft.category.title',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'required' => false,
            ])
            ->add('customCategory', TextType::class, [
                'label'    => 'emergency.business_draft.custom_category.title',
                'required' => false,
            ])
            ->add('services', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'emergency.business_draft.service.placeholder',
                    'multiple' => true,
                ],
                'class' => 'Domain\EmergencyBundle\Entity\EmergencyService',
                'label' => 'emergency.business_draft.service.title',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'emergency.business_draft.phone.title',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                        'message' => 'business_profile.phone.digit_dash',
                    ]),
                ],
            ])
            ->add('area', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control',
                ],
                'class' => 'Domain\EmergencyBundle\Entity\EmergencyArea',
                'label' => 'emergency.business_draft.area.title',
                'label_attr' => [
                    'class' => 'title-label'
                ],
            ])
            ->add('address', TextType::class, [
                'label'    => 'emergency.business_draft.address',
                'required' => true,
            ])
            ->add('customWorkingHours', TextType::class, [
                'label' => 'emergency.business_draft.working_hours',
                'required' => false,
            ])
            ->add('paymentMethods', EntityType::class, [
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple',
                    'placeholder' => 'emergency.business_draft.payment.placeholder',
                    'multiple' => true,
                ],
                'class' => 'Domain\BusinessBundle\Entity\PaymentMethod',
                'label' => 'emergency.business_draft.payment.title',
                'label_attr' => [
                    'class' => 'title-label'
                ],
                'multiple' => true,
                'query_builder' => function (PaymentMethodRepository $repository) {
                    return $repository->getAvailablePaymentMethodsQb();
                },
                'required' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\EmergencyBundle\Entity\EmergencyDraftBusiness',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_emergency_bundle_emergency_draft_business_type';
    }
}
