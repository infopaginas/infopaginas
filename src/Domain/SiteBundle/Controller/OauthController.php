<?php

namespace Domain\SiteBundle\Controller;

use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\OAuthUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class OAuthController
 * @package Domain\SiteBundle\Controller
 */
class OAuthController extends \HWI\Bundle\OAuthBundle\Controller\ConnectController
{
    const AUTH_REMEMBERED_STATUS = 'IS_AUTHENTICATED_REMEMBERED';

    const ERROR_CANT_CONNECT_ACCOUNT_MESSAGE = 'Cannot connect an account.';

    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request
     * @param string $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->getParameter('hwi_oauth.connect');

        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->isGranted(self::AUTH_REMEMBERED_STATUS);

        if (!$hasUser) {
            throw new AccessDeniedException(self::ERROR_CANT_CONNECT_ACCOUNT_MESSAGE);
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->getOauthUtils()->getServiceAuthUrl($request, $resourceOwner)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }

        // Redirect to the login path if the token is empty (Eg. User cancelled auth)
        if (null === $accessToken) {
            return $this->redirectToRoute($this->getParameter('hwi_oauth.failed_auth_path'));
        }

        $userInformation = $resourceOwner->getUserInformation($accessToken);

        /** @var $currentToken OAuthToken */
        $currentToken = $this->getToken();
        $currentUser = $currentToken->getUser();

        $this->getOauthAccountConnector()->connect($currentUser, $userInformation);

        if ($currentToken instanceof OAuthToken) {
            // Update user token with new details
            $this->authenticateUser($request, $currentUser, $service, $currentToken->getRawToken(), false);
        }

        return $this->redirect($this->generateUrl('domain_site_home_index'));
    }

    /**
     * @return OAuthUtils
     */
    private function getOauthUtils() : OAuthUtils
    {
        return $this->get('hwi_oauth.security.oauth_utils');
    }

    /**
     * @return AccountConnectorInterface
     */
    private function getOauthAccountConnector() : AccountConnectorInterface
    {
        return $this->get('hwi_oauth.account.connector');
    }
}
