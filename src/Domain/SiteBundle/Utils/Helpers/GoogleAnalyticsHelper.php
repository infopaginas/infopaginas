<?php

namespace Domain\SiteBundle\Utils\Helpers;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class GoogleAnalyticsHelper
 * @package Domain\SiteBundle\Utils\Helpers
 */
class GoogleAnalyticsHelper
{
    public static function getUserRoleForAnalytics(array $roles)
    {
        /** @var RoleInterface[] $userRoles */
        $userRoles = array_map(function ($roleInterface) {
            return $roleInterface->getRole();
        }, $roles);

        if (in_array('ROLE_ADMINISTRATOR', $userRoles)
            || in_array('ROLE_SALES_MANAGER', $userRoles)
            || in_array('ROLE_CONTENT_MANAGER', $userRoles)
        ) {
            return 'ADMIN';
        } else {
            return 'USER';
        }
    }
}
