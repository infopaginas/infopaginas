<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;
use Domain\BusinessBundle\Repository\BusinessProfileSuggestEditRepository;
use Domain\BusinessBundle\Util\ArrayUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminBusinessSuggestEditsType
 *
 * @package Domain\BusinessBundle\Form\Type
 */
class AdminBusinessSuggestEditsType extends AbstractType
{
    const ACTION_CHOICES = [
        BusinessProfileSuggestEdit::STATUS_ACCEPTED => 'Accept',
        BusinessProfileSuggestEdit::STATUS_REJECTED => 'Reject',
    ];

    /* @var BusinessProfileSuggestEdit[] */
    private $suggestEditList;

    /* @var BusinessProfileSuggestEditRepository */
    private $suggestEditRepository;

    /* @var BusinessProfile */
    private $businessProfile;

    /* @var string */
    private $key;

    /**
     * AdminBusinessSuggestEditsType constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->suggestEditRepository = $container->get('doctrine.orm.entity_manager')
            ->getRepository(BusinessProfileSuggestEdit::class);

        $request = $container->get('request');

        $this->businessProfile = $request->attributes->get('businessProfile');
        $this->key = $request->attributes->get('key');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('suggestEdits', CollectionType::class);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                foreach ($this->getSuggestEditList() as $suggestEdit) {
                    $form
                        ->get('suggestEdits')
                        ->add(
                            $suggestEdit->getId(),
                            ChoiceType::class,
                            [
                                'label'       => $suggestEdit->getValue(),
                                'required'    => true,
                                'choices'     => self::ACTION_CHOICES,
                                'expanded'    => true,
                                'constraints' => [new NotBlank()],
                            ]
                        );
                }
            }
        );
    }

    /**
     * @return array|BusinessProfileSuggestEdit[]
     */
    private function getSuggestEditList()
    {
        if ($this->suggestEditList === null) {
            $this->suggestEditList = $this->suggestEditRepository->getOpenedSuggestsByBusinessAndKey(
                $this->businessProfile,
                $this->key
            );

            $this->suggestEditList = ArrayUtil::useIdInKeys($this->suggestEditList);
        }

        return $this->suggestEditList;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_admin_business_suggest_edits_type';
    }
}
