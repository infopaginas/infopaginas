<?php
namespace Domain\BusinessBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntityHiddenType
 * @package Domain\BusinessBundle\Form\Type
 */
class EntityHiddenType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToIdTransformer($this->objectManager, $options['class']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['class'])
            ->setDefaults([
                'invalid_message' => 'The entity does not exist.',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';
    }
}
