<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class BusinessSuggestEditsType
 *
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessSuggestEditsType extends AbstractType
{
    const STATUS_CHOICES = [
        'Permanently closed'            => 'Permanently closed',
        'Does not exist'                => 'Does not exist',
        'Spam'                          => 'Spam',
        'Private'                       => 'Private',
        'Moved'                         => 'Moved',
        'Duplicate of another business' => 'Duplicate of another business',
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['name'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('website', TextType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['website'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('phones', TextareaType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['phones'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('workingHours', TextType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['workingHours'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('streetAddress', TextType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['streetAddress'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('socialLinks', TextareaType::class, [
                'label'      => BusinessProfileSuggestEdit::KEY_LABELS['socialLinks'],
                'required'   => false,
                'label_attr' => [
                    'class' => 'center-blue-label',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label'    => BusinessProfileSuggestEdit::KEY_LABELS['status'],
                'required' => false,
                'choices'  => self::STATUS_CHOICES,
                'attr'     => [
                    'data-select2-init' => '',
                ],
            ])
            ->add('map', CheckboxType::class, [
                'label'    => BusinessProfileSuggestEdit::KEY_LABELS['map'],
                'required' => false,
                'attr'     => [
                    'data-ignore-field-active' => '',
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr'        => [
                'class' => 'form',
                'id'    => 'createSuggestEditsForm',
            ],
            'constraints' => [
                new Callback([$this, 'validate']),
            ],
        ]);
    }

    /**
     * @param array                     $data
     * @param ExecutionContextInterface $context
     */
    public function validate(array $data, ExecutionContextInterface $context)
    {
        if (implode('', $data) === '') {
            $context->addViolation('form.suggestEdits.empty');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_suggest_edits_type';
    }
}
