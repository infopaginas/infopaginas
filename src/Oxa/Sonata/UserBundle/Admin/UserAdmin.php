<?php
namespace Oxa\Sonata\UserBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\SiteBundle\Validator\Constraints\ConstraintUrlExpanded;
use Domain\SiteBundle\Validator\Constraints\ContainsEmailExpandedValidator;
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
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex;

class UserAdmin extends OxaAdmin
{
    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by' => 'email',
    );

    protected $formOptions = array(
        'validation_groups' => ['Default']
    );

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

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
            ->addIdentifier('firstname')
            ->addIdentifier('lastname')
            ->add('email')
            ->add('role.name')
            ->add('enabled', null, array('editable' => false))
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
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('role')
            ->add('enabled', null, ['label' => 'filter.label_enabled'], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ],
                'translation_domain' => 'SonataUserBundle'
            ])
            ->add('createdAt', 'doctrine_orm_datetime_range', [
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'format' => 'dd-MM-y hh:mm:ss'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('email')
            ->end()
            ->with('Security')
                ->add('role')
                ->add('enabled')
            ->end()
            ->with('Profile')
                ->add('firstname')
                ->add('lastname')
                ->add('locale')
            ->end()
            ->with('Social')
                ->add('facebookURL')
                ->add('twitterURL')
                ->add('googleURL')
                ->add('youtubeURL')
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
        if ($user->getRole() != null &&
            $loggedUser->getRole()->getCode() <= $user->getRole()->getCode() &&
            $loggedUser->getRole()->getCode() <= Group::CODE_CONTENT_MANAGER &&
            $loggedUser->getId() != $user->getId()
        ) {
            $editUserRoleAccess = true;
        }

        // check access an create page
        if ($user->getRole() == null &&
            $loggedUser->getRole()->getCode() <= Group::CODE_CONTENT_MANAGER
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
                ->end()
            ;
        }

        $formMapper
            ->with('General')
                ->add('email', 'email', [
                    'required' => true,
                    'pattern' => ContainsEmailExpandedValidator::EMAIL_REGEX_PATTERN,
                ])
                ->add('plainPassword', 'text', [
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                ])
                ->add('phone')
            ->end()
            ->with('Profile')
                ->add('firstname', null, [
                    'required' => true,
                ])
                ->add('lastname', null, [
                    'required' => true
                ])
            ->end()
            ->with('Social')
                ->add('facebookURL')
                ->add('twitterURL')
                ->add('googleURL')
                ->add('youtubeURL')
            ->end()
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('twitterURL')
                ->addConstraint(new ConstraintUrlExpanded())
            ->end()
            ->with('facebookURL')
                ->addConstraint(new ConstraintUrlExpanded())
            ->end()
            ->with('googleURL')
                ->addConstraint(new ConstraintUrlExpanded())
            ->end()
            ->with('youtubeURL')
                ->addConstraint(new ConstraintUrlExpanded())
            ->end()
            ->with('phone')
                ->addConstraint(new Regex(BusinessProfilePhone::REGEX_PHONE_PATTERN))
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
}
