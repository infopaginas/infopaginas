<?php

namespace Application\Sonata\UserBundle\Admin;

use Application\Sonata\AdminBundle\Admin\ApplicationAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\UserBundle\Admin\Model\GroupAdmin as BaseGroupAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class GroupAdmin extends ApplicationAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $formOptions = array(
        'validation_groups' => 'Registration',
    );

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', array());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('roles')
            ->add('isActive', null, ['editable' => true]);
        $this->addGridActions($listMapper);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->end()
            ->with('Security', array('class' => 'col-md-6'))
            ->add('roles', 'sonata_security_roles', array(
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->end();
    }
}
