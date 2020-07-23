<?php

namespace Domain\PageBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Manager\FeedbackReportManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class FeedbackFormType
 * @package Domain\PageBundle\Form\Type
 */
class FeedbackFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'isReportProblem'    => false,
                'allow_extra_fields' => true,
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label'    => 'contact.form.user_full_name',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                       'max' => FeedbackReportManager::MAX_LENGTH_FULL_NAME,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'contact.form.email',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email([
                        'strict' => true,
                    ]),
                    new Length([
                        'max' => FeedbackReportManager::MAX_LENGTH_EMAIL,
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label'    => 'contact.form.message',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => FeedbackReportManager::MAX_LENGTH_MESSAGE,
                    ]),
                ],
            ])
            ->add('isReportProblem', TextType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => ['class' => 'hidden'],
                'data'     => $options['isReportProblem']
            ])
            ->add('businessName', TextType::class, [
                'label'    => 'contact.form.business_name',
                'required' => false,
                'attr'     => ['class' => $options['isReportProblem'] ? 'hidden' : ''],
                'constraints' => [
                    new Length([
                        'max' => FeedbackReportManager::MAX_LENGTH_BUSINESS_NAME,
                    ]),
                ],
            ])
        ;

        if (!$options['isReportProblem']) {
            $builder
                ->add('phone', TextType::class, [
                    'label' => 'contact.form.phone',
                    'attr'  => [
                        'class' => 'phone-mask',
                    ],
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                            'message' => 'business_profile.phone.digit_dash',
                        ]),
                    ]
                ])
                ->add('subject', ChoiceType::class, [
                    'label'     => 'contact.form.subject',
                    'multiple'  => false,
                    'choices'   => Page::getContactSubjects(),
                    'choice_translation_domain' => true,
                ])
            ;
        }
    }
}
