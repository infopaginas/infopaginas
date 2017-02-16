<?php

namespace Oxa\VideoBundle\Controller;

use Oxa\VideoBundle\Manager\YoutubeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OAuthController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function notifyAction()
    {
        // notify user about youtube oauth restriction
        return $this->render(':redesign:oauth-youtube-notify.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function oauthAction()
    {
        $youtubeManager = $this->getYoutubeManager();

        $authUrl = $youtubeManager->getAuthUrl();

        return new RedirectResponse($authUrl);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function oauthRedirectAction(Request $request)
    {
        $code = $request->query->get('code');

        $data = $this->getYoutubeManager()->handleUserAuthByCode($code);

        if ($data['error'] === false and $data['status']) {
            $route = 'oxa_youtube_oauth_success';
        } else {
            $route = 'oxa_youtube_oauth_error';
        }

        return new RedirectResponse(
            $this->generateUrl(
                $route,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authSuccessAction()
    {
        return $this->render(
            ':redesign:oauth-youtube-result.html.twig',
            [
                'result' => 'Success',
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authErrorAction()
    {
        return $this->render(
            ':redesign:oauth-youtube-result.html.twig',
            [
                'result' => 'Ops, something went wrong',
            ]
        );
    }

    /**
     * @return YoutubeManager
     */
    private function getYoutubeManager() : YoutubeManager
    {
        return $this->get('oxa.manager.video.youtube');
    }
}
