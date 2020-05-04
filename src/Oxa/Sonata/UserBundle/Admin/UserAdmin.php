<?php
namespace Oxa\Sonata\UserBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\SiteBundle\Validator\Constraints\ConstraintUrlExpanded;
use Domain\SiteBundle\Validator\Constraints\ContainsEmailExpandedValidator;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\Sonata\UserBundle\OxaSonataUserBundle;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex;

class UserAdmin extends OxaAdmin
{
    public $advancedFilterMode = true;

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
            ->add('createdAt')
            ->add(
                'businessesCount',
                null,
                [
                    'label' => 'Number of Businesses',
                ]
            )
        ;

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
            ->add('enabled')
            ->add('createdAt', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
            ->add(
                'businessesCount',
                null,
                [
                    'label' => 'Number of Businesses',
                ]
            )
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
            ->with('Managed Businesses')
                ->add(
                    'businessProfiles',
                    null,
                    [
                        'label' => 'Managed Businesses',
                    ]
                )
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
            ->tab('Profile')
                ->with('Profile', array('class' => 'col-md-6'))->end()
                ->with('General', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Reviews', array('class' => 'col-md-6'))
                ->with('User Reviews')->end()
            ->end()
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
                ->getRepository(Group::class)
                ->getEqualOrLowerPriorityRoles($loggedUser->getRole()->getCode())
            ;

            // add to group zoning
            $formMapper
                ->tab('Profile')
                    ->with('Security', array('class' => 'col-md-6'))
                    ->end()
                ->end()
            ;

            $formMapper
                ->tab('Profile')
                    ->with('Security')
                        ->add('role', EntityType::class, [
                            'class'   => Group::class,
                            'choices' => $roles
                        ])
                        ->add('enabled')
                    ->end()
                ->end()
            ;
        }

        $formMapper
            ->tab('Profile')
                ->with('General')
                    ->add(
                        'email',
                        'email',
                        [
                            'required' => true,
                            'pattern'  => ContainsEmailExpandedValidator::EMAIL_REGEX_PATTERN,
                        ]
                    )
                    ->add(
                        'plainPassword',
                        'text',
                        [
                            'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                        ]
                    )
                    ->add('phone')
                    ->add(
                        'location',
                        'text',
                        [
                            'label'    => 'Location',
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Profile')
                    ->add(
                        'firstname',
                        null,
                        [
                            'label'     => 'First Name',
                            'required'  => true,
                        ]
                    )
                    ->add(
                        'lastname',
                        null,
                        [
                            'label'     => 'Last Name',
                            'required'  => true,
                        ]
                    )
                ->end()
            ->end()
            ->tab('Reviews')
                ->with('User Reviews')
                    ->add(
                        'businessReviews',
                        CollectionType::class,
                        [
                            'label'        => 'Businesses Reviews',
                            'by_reference' => true,
                            'mapped'       => true,
                            'btn_add'      => false,
                            'disabled'     => true,
                            'type_options' => [
                                'delete' => false,
                            ]
                        ],
                        [
                            'edit'         => 'inline',
                            'inline'       => 'table',
                            'allow_delete' => false,
                        ]
                    )
                ->end()
            ->end()
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
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
