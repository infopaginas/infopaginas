<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 13.07.16
 * Time: 20:50
 */

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessGalleryType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessGalleryType extends AbstractType
{
    protected $container;
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
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control select-control select-image-type',
                ],
                'choices' => $this->getAllowedMediaTypes(),
                'expanded' => false,
                'label' => 'Type',
                'multiple' => false,
            ])
        ;
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

    protected function getRequest()
    {
        return $this->container->get('request');
    }

    protected function getBusinessProfileManager()
    {
        return $this->container->get('domain_business.manager.business_profile');
    }

    protected function getAllowedMediaTypes()
    {
        $businessProfileId = $this->getRequest()->get('id', false);

        if (!$businessProfileId && 0) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $subscription = $businessProfile->getSubscriptionPlan();
        if (!$subscription) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $options = [
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO => 'Logo',
        ];

        if (SubscriptionPlanInterface::CODE_PREMIUM_GOLD === $subscription->getCode()
            ||
            SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM === $subscription->getCode()
        ){
            $options[OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES]    = 'Photo';
            $options[OxaMediaInterface::CONTEXT_BANNER]                     = 'Banner Ad';
        }

        return $options;
    }
}
