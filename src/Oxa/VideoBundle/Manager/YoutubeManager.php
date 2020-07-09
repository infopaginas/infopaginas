<?php

namespace Oxa\VideoBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\ConfigBundle\Entity\Config as SystemConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class YoutubeManager
{
    public const YOUTUBE_ID_COLUMN_ALIAS = 'youtube_id';

    public const ERROR_BAD_REQUEST     = 400;
    public const ERROR_LOGIN_REQUIRED  = 401;
    public const ERROR_FORBIDDEN       = 403;
    public const ERROR_NOT_FOUND       = 404;
    public const ERROR_BAD_TOKEN       = 0;

    public const ERROR_UNKNOWN         = 'ERROR_UNKNOWN';
    public const ERROR_ASSET_NOT_EXIST = 'ERROR_ASSET_NOT_EXIST';
    public const ERROR_INVALID_ACCOUNT = 'ERROR_INVALID_ACCOUNT';

    private const ACCESS_TYPE_OFFLINE = 'offline';
    private const APPROVAL_PROMPT_FORCE = 'force';

    //see https://developers.google.com/youtube/v3/docs/channels/list
    // default 5, min 0, max 50
    private const CHANNEL_LIST_MAX_RESULT = 50;
    private const VIDEOS_LIST_MAX_RESULT  = 50;

    // see https://developers.google.com/youtube/v3/docs/videoCategories/list
    // "Film & Animation" - category id = 1
    private const DEFAULT_CATEGORY_ID = 1;

    /**
     * see https://developers.google.com/youtube/v3/guides/uploading_a_video
     *
     * 1024 * 1024 (1 megabyte)
     */
    private const CHUNK_SIZE_BYTES = 1048576;

    /* @var ContainerInterface $container */
    private $container;

    /* @var VideoMediaManager $videoMediaManager */
    private $videoMediaManager;

    /* @var VideoManager $videoManager */
    private $videoManager;

    /* @var \Google_Client $client */
    private $client;

    /* @var Config */
    private $configService;

    /* @var EntityManager */
    private $em;

    private $redirectUrl;

    private $privacyStatus;

    private $channelId;

    private $logger;

    public function __construct(ContainerInterface $container, $privacyStatus, $channelId)
    {
        $this->container = $container;
        $this->videoMediaManager = $container->get('oxa.manager.video_media');
        $this->videoManager      = $container->get('oxa.manager.video');
        $this->configService     = $container->get('oxa_config');
        $this->em                = $container->get('doctrine.orm.entity_manager');
        $this->logger            = $container->get('logger');

        $this->redirectUrl = $container->get('router')->generate(
            'oxa_youtube_oauth_redirect',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->privacyStatus = $privacyStatus;
        $this->channelId     = $channelId;
    }

    public function setGoogleClient($clientId, $secretKey)
    {
        $this->client = new \Google_Client();

        $this->client->setClientId($clientId);
        $this->client->setClientSecret($secretKey);
        $this->client->setAccessType(self::ACCESS_TYPE_OFFLINE);
        $this->client->setScopes(\Google_Service_YouTube::YOUTUBE);
        $this->client->setRedirectUri($this->redirectUrl);
        $this->client->setApprovalPrompt(self::APPROVAL_PROMPT_FORCE);
    }

    public function removeMedia(VideoMedia $videoMedia)
    {
        $videoId = $videoMedia->getYoutubeId();
        $error   = false;
        $status  = false;

        try {
            $youtube = new \Google_Service_YouTube($this->client);

            // see https://developers.google.com/youtube/v3/docs/videos/delete
            $response = $youtube->videos->delete($videoId);
            $status   = true;
        } catch (\Google_Service_Exception $e) {
            if (in_array($e->getCode(), $this->getYoutubeErrors())) {
                $error = $e->getCode();
            }
        } catch (\Google_Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
        }

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    public function updateMedia(VideoMedia $videoMedia)
    {
        //only title and description can be updated
        $title       = $videoMedia->getYoutubeTitle();
        $description = $videoMedia->getYoutubeDescription();
        $videoId     = $videoMedia->getYoutubeId();
        $error       = false;
        $status      = false;

        try {
            $youtube = new \Google_Service_YouTube($this->client);

            // Call the API's videos.list method to retrieve the video resource.
            $listResponse = $youtube->videos->listVideos(
                'snippet',
                [
                    'id' => $videoId,
                ]
            );

            // If $listResponse is empty, the specified video was not found.
            // Since the request specified a video ID, the response only contains one video resource.
            if (!is_object($listResponse) or !method_exists($listResponse, 'getItems') or !$listResponse->getItems()) {
                $error = self::ERROR_NOT_FOUND;
            } else {
                $video = $listResponse[0];

                $video['snippet']['title'] = $title;
                $video['snippet']['description'] = $description;

                // Update the video resource by calling the videos.update() method.
                // see https://developers.google.com/youtube/v3/docs/videos/update
                $updateResponse = $youtube->videos->update('snippet', $video);
                $status = true;
            }
        } catch (\Google_Service_Exception $e) {
            if (in_array($e->getCode(), $this->getYoutubeErrors())) {
                $error = $e->getCode();
            }
        } catch (\Google_Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
        }

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    /*
     * Quota cost depends on $part: https://developers.google.com/youtube/v3/docs/playlists/list#part
     */
    public function getVideosInfo(array $ids, string $part = 'id')
    {
        $error = false;
        $data  = [];

        try {
            $youtube = new \Google_Service_YouTube($this->client);

            $ids = array_column($ids, self::YOUTUBE_ID_COLUMN_ALIAS);
            foreach (array_chunk($ids, self::VIDEOS_LIST_MAX_RESULT) as $idsArray) {
                $idsString = implode(',', $idsArray);
                $response  = $youtube->videos->listVideos($part, [
                    'id' => $idsString,
                ]);

                foreach ($response->getItems() as $item) {
                    $data[$item->getId()] = $item->getStatus()->getUploadStatus();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $error = $e->getMessage();
        }

        return [
            'data'  => $data,
            'error' => $error,
        ];
    }

    public function uploadMedia(VideoMedia $videoMedia)
    {
        $title       = $videoMedia->getYoutubeTitle();
        $description = $videoMedia->getYoutubeDescription();
        $error       = false;
        $youtubeId   = '';

        $url = $this->videoManager->getPublicUrl($videoMedia);

        $tempFilePath = $this->videoManager->uploadTempYoutubeFile($url);

        if ($tempFilePath) {
            try {
                $youtube = new \Google_Service_YouTube($this->client);
                $snippet = new \Google_Service_YouTube_VideoSnippet();

                $snippet->setTitle($title);
                $snippet->setDescription($description);

                // Numeric video category. See https://developers.google.com/youtube/v3/docs/videoCategories/list
                // Default category id = 22 "People & Blogs"
                // see https://developers.google.com/youtube/v3/guides/uploading_a_video
                $snippet->setCategoryId(self::DEFAULT_CATEGORY_ID);

                // Set the video's status to "public". Valid statuses are "public", "private" and "unlisted".
                $status = new \Google_Service_YouTube_VideoStatus();

                $status->privacyStatus = $this->privacyStatus;

                // Associate the snippet and status objects with a new video resource.
                $video = new \Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);

                // Specify the size of each chunk of data, in bytes. Set a higher value for
                // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // value for better recovery on less reliable connections.
                $chunkSizeBytes = self::CHUNK_SIZE_BYTES;

                // Setting the defer flag to true tells the client to return a request
                // which can be called with ->execute();instead of making the API call immediately.
                $this->client->setDefer(true);

                // Create a request for the API's videos.insert method to create and upload the video.
                $insertRequest = $youtube->videos->insert('status,snippet', $video);

                // Create a MediaFileUpload object for resumable uploads.
                $media = new \Google_Http_MediaFileUpload(
                    $this->client,
                    $insertRequest,
                    'video/*',
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($tempFilePath));

                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($tempFilePath, 'rb');
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }

                fclose($handle);

                $youtubeId = $status->id;
            } catch (\Google_Service_Exception $e) {
                if (in_array($e->getCode(), $this->getYoutubeErrors())) {
                    $error = $e->getCode();
                }
            } catch (\Google_Exception $e) {
                $error = $e->getMessage();
                $this->logger->error($error);
            }

            unlink($tempFilePath);
        } else {
            $error = self::ERROR_ASSET_NOT_EXIST;
        }

        return [
            'youtubeId' => $youtubeId,
            'error'     => $error,
        ];
    }

    public function checkYoutubeAccount()
    {
        $error  = false;
        $status = false;

        try {
            $youtube = new \Google_Service_YouTube($this->client);

            $listChannels = $youtube->channels->listChannels(
                'snippet',
                [
                    'mine' => true,
                    'maxResults' => self::CHANNEL_LIST_MAX_RESULT,
                ]
            );

            $status = $this->checkChannelResponse($listChannels, $youtube);

            if ($status === false) {
                $error = self::ERROR_INVALID_ACCOUNT;
            }

        } catch (\Google_Service_Exception $e) {
            if (in_array($e->getCode(), $this->getYoutubeErrors())) {
                $error = $e->getCode();
            }
        } catch (\Google_Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
        }

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleUserAuthByCode($code)
    {
        $error  = false;
        $status = false;

        try {
            $token = $this->client->authenticate($code);

            $this->client->setAccessToken($token);

            //check account
            $check = $this->checkYoutubeAccount();

            if ($check['error'] === false and $check['status']) {
                $config = $this->em->getRepository(SystemConfig::class)->findOneBy(
                    [
                        'key' => ConfigInterface::YOUTUBE_ACCESS_TOKEN,
                    ]
                );

                if ($config) {
                    $config->setValue($token);
                    $this->em->flush($config);

                    $status = true;
                }
            } else {
                $error = $check['error'];
            }
        } catch (\Google_Auth_Exception $e) {
            $error  = self::ERROR_UNKNOWN;
        }

        return [
            'status' => $status,
            'error'  => $error,
        ];
    }

    public function handleUserTokenAuth()
    {
        $error  = false;
        $status = false;

        $token = $this->configService->getValue(ConfigInterface::YOUTUBE_ACCESS_TOKEN);

        try {
            $this->client->setAccessToken($token);
            $status = true;
        } catch (\Google_Auth_Exception $e) {
            if (in_array($e->getCode(), $this->getYoutubeErrors())) {
                $error = $e->getCode();
            }
        } catch (\Google_Exception $e) {
            $error = $e->getMessage();
            $this->logger->error($error);
        }

        if ($error !== false) {
            $check = [
                'status' => $status,
                'error'  => $error,
            ];
        } else {
            $check = $this->checkYoutubeAccount();
        }

        return $check;
    }

    public function checkAccessToken($token)
    {
        $data = json_decode($token);

        if ($data and !empty($data->access_token) and !empty($data->refresh_token)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getYoutubeErrors()
    {
        return [
            self::ERROR_BAD_REQUEST,
            self::ERROR_LOGIN_REQUIRED,
            self::ERROR_FORBIDDEN,
            self::ERROR_NOT_FOUND,
            self::ERROR_BAD_TOKEN,
        ];
    }

    protected function checkChannelResponse(\Google_Service_YouTube_ChannelListResponse $listChannels, $youtube)
    {
        foreach ($listChannels->getItems() as $item) {
            if ($item->id == $this->channelId) {
                return true;
            }
        }

        return false;
    }
}
