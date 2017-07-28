<?php

namespace Oxa\Sonata\UserBundle\EventListener;

use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\UserActionModel;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    /**
     * @param AuthorizationChecker    $security
     * @param UserActionReportManager $userActionReportManager
     */
    public function __construct(AuthorizationChecker $security, UserActionReportManager $userActionReportManager) {
        $this->security = $security;
        $this->userActionReportManager = $userActionReportManager;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->security->isGranted('ROLE_SALES_MANAGER') or $this->security->isGranted('ROLE_ADMIN')) {
            $this->userActionReportManager->registerUserAction(UserActionModel::TYPE_ACTION_LOGIN, [
                'entity' => UserActionModel::ENTITY_TYPE_AUTH,
                'type'   => UserActionModel::TYPE_ACTION_LOGIN,
            ]);
        }
    }
}