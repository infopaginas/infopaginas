<?php

namespace Oxa\Sonata\MediaBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class BaseMediaAdmin
 * @package Oxa\Sonata\MediaBundle\Admin
 */
class BaseMediaAdmin extends OxaAdmin
{
    protected $pool;

    /**
     * @param string                            $code
     * @param string                            $class
     * @param string                            $baseControllerName
     * @param Pool $pool
     */
    public function __construct($code, $class, $baseControllerName, Pool $pool)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
            ->add('enabled')
            ->add('size')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $media = $this->getSubject();

        if (!$media) {
            $media = $this->getNewInstance();
        }

        if (!$media || !$media->getProviderName()) {
            return;
        }

        $formMapper->getFormBuilder()->addModelTransformer(
            new ProviderDataTransformer($this->pool, $this->getClass()),
            true
        );

        $provider = $this->pool->getProvider($media->getProviderName());

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($media)
    {
        $parameters = $this->getPersistentParameters();
        $media->setContext($parameters['context']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }

        $filterParams = $this->getRequest()->get('filter');
        $context = $filterParams['context']['value'] ??
            $this->getRequest()->get('context', $this->pool->getDefaultContext());

        $providers = $this->pool->getProvidersByContext($context);
        $provider  = $this->getRequest()->get('provider');

        // if the context has only one provider, set it into the request
        // so the intermediate provider selection is skipped
        if (count($providers) == 1 && null === $provider) {
            $provider = array_shift($providers)->getName();
            $this->getRequest()->query->set('provider', $provider);
        }

        return array(
            'provider' => $provider,
            'context'  => $context,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $media = parent::getNewInstance();

        if ($this->hasRequest()) {
            $media->setProviderName($this->getRequest()->get('provider'));
            $media->setContext($this->getRequest()->get('context'));
        }

        return $media;
    }

    /**
     * @return null|Pool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('providerReference')
            ->add('enabled')
            ->add('context')
        ;

        $providers = array();

        $providerNames = (array) $this->pool->getProviderNamesByContext(
            $this->getPersistentParameter('context', $this->pool->getDefaultContext())
        );
        foreach ($providerNames as $name) {
            $providers[$name] = $name;
        }

        $datagridMapper->add(
            'providerName',
            ChoiceFilter::class,
            array(
                'field_options' => array(
                    'choices'  => $providers,
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                ),
                'field_type' => ChoiceType::class,
            )
        );
    }
}
