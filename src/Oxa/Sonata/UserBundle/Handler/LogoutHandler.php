<?php

namespace Oxa\Sonata\UserBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\UserActionModel;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class AuthenticationHandler
 * @package Oxa\Sonata\UserBundle\Handler
 */
class LogoutHandler extends DefaultLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @param HttpUtils               $httpUtils
     * @param AuthorizationChecker    $security
     * @param UserActionReportManager $userActionReportManager
     */
    public function __construct(
        HttpUtils $httpUtils,
        AuthorizationChecker $security,
        UserActionReportManager $userActionReportManager
    ) {
        $this->security = $security;
        $this->userActionReportManager = $userActionReportManager;

        parent::__construct($httpUtils);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($this->security->isGranted('ROLE_SALES_MANAGER') or $this->security->isGranted('ROLE_ADMIN')) {
            $this->userActionReportManager->registerUserAction(UserActionModel::TYPE_ACTION_LOGOUT, [
                'entity' => UserActionModel::ENTITY_TYPE_AUTH,
                'type'   => UserActionModel::TYPE_ACTION_LOGOUT,
            ]);
        }

        return parent::onLogoutSuccess($request);
    }
}
