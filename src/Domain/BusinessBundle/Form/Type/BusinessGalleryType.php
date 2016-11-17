<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 13.07.16
 * Time: 20:50
 */

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessGalleryType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessGalleryType extends AbstractType
{
    /** @var ContainerInterface $container */
    protected $container;

    /**
     * Gallery Type From constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('media', EntityHiddenType::class, [
                'class' => 'Oxa\Sonata\MediaBundle\Entity\Media',
                'attr' => ['class' => 'hidden-media'],
            ])
            ->add('isPrimary', CheckboxType::class, [
                'attr' => [
                    'class' => 'is-primary',
                ],
                'label' => 'Primary'
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control textarea-control',
                    'rows' => 3,
                ],
                'label' => 'Description',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var BusinessProfile $businessProfile */

            $businessGallery = $event->getData();

            $businessProfile = $businessGallery !== null ? $businessGallery->getBusinessProfile() : new BusinessProfile();

            $subscription = (new SubscriptionPlan())->setCode(SubscriptionPlanInterface::CODE_FREE);

            if ($businessProfile !== null && $businessProfile->getSubscriptionPlan() !== null) {
                $subscription = $businessProfile->getSubscriptionPlan();
            }

            switch ($subscription->getCode()) {
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
            }
        });

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\BusinessBundle\Entity\Media\BusinessGallery',
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_gallery_type';
    }

    protected function setupPremiumPlusPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $options = [
                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO    => 'Logo',
            ];
        $form
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-image-type',
                ],
                'choices'  => $options,
                'expanded' => false,
                'label'    => 'Type',
                'multiple' => false,
            ])
        ;

        return $options;
    }

    protected function setupPremiumGoldPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $options = [
                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES  => 'Photo',
                OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO    => 'Logo',
                OxaMediaInterface::CONTEXT_BANNER                   => 'Banner Ad',
            ];
        $form
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-image-type',
                ],
                'choices'  => $options,
                'expanded' => false,
                'label'    => 'Type',
                'multiple' => false,
            ])
        ;

        return $options;
    }

    protected function setupPremiumPlatinumPlanFormFields(BusinessProfile $businessProfile, FormInterface $form)
    {
        $this->setupPremiumGoldPlanFormFields($businessProfile, $form);
    }
}
