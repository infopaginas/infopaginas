<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 6/8/16
 * Time: 12:07 PM
 */

namespace Domain\BusinessBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class GoogleMapType
 * @package Domain\BusinessBundle\Form\Type
 */
class GoogleMapType extends AbstractType
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['required'] = $options['required'];
        $view->vars['language'] = $options['language'];
        $view->vars['latitude'] = $options['latitude'];
        $view->vars['longitude'] = $options['longitude'];
        $view->vars['zoom'] = $options['zoom'];
        $view->vars['google_api_key'] = $options['google_api_key'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'language' => 'en',
            'latitude' => 18.4248008,
            'longitude' => -66.1185967,
            'zoom' => 12,
            'google_api_key' => '',
        ]);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'google_map';
    }
}
