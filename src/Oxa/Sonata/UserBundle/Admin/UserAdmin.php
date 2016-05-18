<?php
namespace Oxa\Sonata\UserBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\Sonata\UserBundle\OxaSonataUserBundle;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAdmin extends OxaAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        if (!$this->getSubject() || is_null($this->getSubject()->getId())) {
            $options['validation_groups'] = 'Registration';
        } else {
            $options['validation_groups'] = 'Profile';
        }

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(parent::getExportFields(), function ($v) {
            return !in_array($v, array('password', 'salt'));
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('username')
            ->addIdentifier('firstname')
            ->addIdentifier('lastname')
            ->add('email')
            ->add('role.name')
            ->add('enabled', null, array('editable' => false))
//            ->add('isActive', null, array('editable' => false))
            ->add('createdAt');

        $this->addGridActions($listMapper);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('role')
            ->add('enabled', null, ['label' => 'filter.label_enabled'], null, ['choices' => [
                1 => 'label_yes',
                2 => 'label_no',
            ], 'translation_domain' => 'SonataUserBundle'
            ])
//            ->add('isActive', null, [], null, ['choices' => [
//                1 => 'label_yes',
//                2 => 'label_no',
//            ]])
            ->add('createdAt', 'doctrine_orm_datetime_range', array(
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => array('format' => 'dd-MM-y hh:mm:ss')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->end()
            ->with('Security')
            ->add('role')
            ->add('enabled')
//                ->add('isActive')
            ->end()
            ->with('Profile')
            ->add('firstname')
            ->add('lastname')
            ->add('locale')
            ->end()
            ->with('Social')
            ->add('facebookUid')
            ->add('facebookName')
            ->add('gplusUid')
            ->add('gplusName')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end()
            ->with('Social', array('class' => 'col-md-6'))->end()
        ;

        /* @var User $loggedUser */
        $loggedUser = $this->getConfigurationPool()->getContainer()
            ->get('security.token_storage')->getToken()->getUser();

        /* @var User $user */
        $user = $this->getSubject();
        
        $editUserRoleAccess = false;
        $createUserRoleAccess = false;
        
        // check access an edit page
        if (
            $user->getRole() != null &&
            $loggedUser->getRole()->getCode() <= $user->getRole()->getCode() &&
            $loggedUser->getRole()->getCode() <= Group::CODE_CONTENT_MANAGER &&
            $loggedUser->getId() != $user->getId()
        ) {
            $editUserRoleAccess = true;
        }

        // check access an create page
        if (
            $loggedUser->getRole()->getCode() <= Group::CODE_CONTENT_MANAGER &&
            $user->getRole() == null
        ) {
            $createUserRoleAccess = true;
        }

        // allowed to edit user's security data:
        // - content_managers and administrators
        // - if your priority higher than user's (smaller number higher)
        // - if it's not your profile
        if ($editUserRoleAccess || $createUserRoleAccess) {
            // get roles with equal or lower priority(code) than you have
            $roles = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine')
                ->getRepository('OxaSonataUserBundle:Group')
                ->getEqualOrLowerPriorityRoles($loggedUser->getRole()->getCode())
            ;

            // add to group zoning
            $formMapper
                ->with('Security', array('class' => 'col-md-6'))->end()
            ;

            $formMapper
                ->with('Security')
                ->add('role', 'entity', [
                    'class' => Group::class,
                    'choices' => $roles
                ])
                ->add('enabled')
//                ->add('groups')
//                ->add('realRoles', 'sonata_security_roles', array('expanded' => true))
                ->end()
            ;
        }

        $formMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'text', array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ))
            ->end()
            ->with('Profile')
            ->add('firstname', null, ['attr' => ['maxlength' => 35]])
            ->add('lastname', null, ['attr' => ['maxlength' => 35]])
            ->add('locale', 'locale')
            ->end()
            ->with('Social')
            ->add('facebookUid')
            ->add('facebookName')
            ->add('gplusUid')
            ->add('gplusName')
            ->end()
        ;
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->remove('copy');
    }
}
