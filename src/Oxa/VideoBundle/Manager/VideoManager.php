<?php

namespace Oxa\VideoBundle\Manager;

use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Gaufrette\Filesystem;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use FFMpeg\Coordinate\TimeCode;

class VideoManager
{
    const DEFAULT_URI_UPLOAD_FILE_EXTENSION = '.mp4';

    const MAX_FILENAME_LENGTH = 240;
    const LINK_LIFE_TIME      = 600;
    const AUDIO_CODEC = 'libmp3lame';
    const TRUSTED_SCORE = 25;
    const POSTER_AT_SECOND = 3;

    private static $allowedMimeTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
        'video/quicktime',
        'video/avi',
        'video/mpeg',
        'video/x-ms-wmv',
        'video/x-flv',
    ];
    
    private $mimeExtensions = [
        'video/mp4'       => '.mp4',
        'video/webm'      => '.webm',
        'video/ogg'       => '.ogv',
        'video/avi'       => '.avi',
        'video/mpeg'      => '.mpg',
        'video/x-ms-wmv'  => '.wmv',
        'video/quicktime' => '.mov',
        'video/x-flv'     => '.flv',
    ];

    private $filesystem;

    private $videoMediaManager;

    public function __construct(
        Filesystem $filesystem,
        VideoMediaManager $videoMediaManager,
        ContainerInterface $container
    ) {
        $this->filesystem = $filesystem;
        $this->videoMediaManager = $videoMediaManager;
        $this->container = $container;
    }

    /**
     * @param VideoMedia $video
     *
     * @return bool
     */
    public function removeVideo($video)
    {
        $status = true;

        if ($video and $video->getBusinessProfiles()->isEmpty()) {
            if ($this->filesystem->getAdapter()->exists($video->getFilepath() . $video->getFilename())) {
                $this->filesystem->delete($video->getFilepath() . $video->getFilename());
            }

            if ($video->getYoutubeSupport() and $video->getYoutubeId()) {
                $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_REMOVE);
            } else {
                $this->container->get('doctrine.orm.entity_manager')->remove($video);
            }
        } else {
            $status = false;
        }

        return $status;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function scheduleVideoForRemove($id)
    {
        $status = true;

        $media = $this->videoMediaManager->find($id);
        $media->setIsDeleted(true);

        return $status;
    }

    public function uploadLocalFile(UploadedFile $file, array $data = []) : VideoMedia
    {
        $fileData = [
            'name'      => $file->getClientOriginalName(),
            'type'      => $file->getClientMimeType(),
            'ext'       => $file->getClientOriginalExtension(),
            'path'      => $file->getPathname(),
        ];

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        return $this->videoMediaManager->save($uploadedFileData);
    }

    /**
     * @param VideoMedia $video
     * @param UploadedFile $file
     *
     * @return VideoMedia
     */
    public function addVideoLocalFile(VideoMedia $video, UploadedFile $file) : VideoMedia
    {
        $fileData = [
            'name'      => $file->getClientOriginalName(),
            'type'      => $file->getClientMimeType(),
            'ext'       => $file->getClientOriginalExtension(),
            'path'      => $file->getPathname(),
        ];

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        $video = $this->addVideoProperties($video, $uploadedFileData);

        return $video;
    }

    public function uploadLocalFileData(array $data) : array
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($data['type'], self::$allowedMimeTypes)) {
            $message = $this->container->get('translator')->trans('You can upload the following file types', [], 'messages');
            throw new \InvalidArgumentException(sprintf($message, $data['type']));
        }

        $adapter = $this->filesystem->getAdapter();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', 1), $data['ext']);
        } while ($adapter->exists($path . $filename));
 
        $adapter->setMetadata($path . $filename, [
            'contentType'   => $data['type'],
            'ACL'           => 'public-read',
        ]);
        $uploadedSize = $adapter->write($path . $filename, file_get_contents($data['path']));;
        if (!$uploadedSize) {
            $message = $this->container->get('translator')
                ->trans('File %s is not uploaded. Please contact administrator', [], 'messages');

            throw new \InvalidArgumentException(sprintf($message, $filename));
        }

        $video = [
            'name'      => $data['name'],
            'filename'  => $filename,
            'type'      => $data['type'],
            'filepath'  => $path,
        ];

        return $video;
    }

    public function uploadRemoteFile(string $url, array $data = [])
    {
        $headers = SiteHelper::checkUrlExistence($url);

        $fileData = [
            'name'      => $this->generateFilenameForUrl($url),
            'ext'       => $this->getExtensionByMime($headers['content_type']),
            'type'      => $headers['content_type'],
            'path'      => $url,
        ];

        if ($fileData['ext'] == null || !isset($headers['content_type'])) {
            $message = $this->container->get('translator')->trans('the link to the video is invalid', [], 'messages');
            throw new \InvalidArgumentException(sprintf($message));
        }

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        return $this->videoMediaManager->save($uploadedFileData);
    }

    /**
     * @param VideoMedia $video
     * @param string     $url
     *
     * @return VideoMedia
     */
    public function addVideoFromRemoteFile(VideoMedia $video, $url)
    {
        $headers = SiteHelper::checkUrlExistence($url);

        $fileData = [
            'name'      => $this->generateFilenameForUrl($url),
            'ext'       => $this->getExtensionByMime($headers['content_type']),
            'type'      => $headers['content_type'],
            'path'      => $url,
        ];

        if ($fileData['ext'] == null || !isset($headers['content_type'])) {
            $message = $this->container->get('translator')->trans('the link to the video is invalid', [], 'messages');
            throw new \InvalidArgumentException(sprintf($message));
        }

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        $video = $this->addVideoProperties($video, $uploadedFileData);

        return $video;
    }

    /**
     * @param VideoMedia $video
     * @param array      $data
     *
     * @return VideoMedia
     */
    protected function addVideoProperties($video, $data)
    {
        $video->setName($data['name']);
        $video->setType($data['type']);
        $video->setFilename($data['filename']);
        $video->setFilepath($data['filepath']);

        $video->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_ADD);

        return $video;
    }

    public function uploadTempYoutubeFile($url)
    {
        $headers = SiteHelper::checkUrlExistence($url);

        $fileData = [
            'name'      => $this->generateFilenameForUrl($url),
            'ext'       => $this->getExtensionByMime($headers['content_type']),
            'type'      => $headers['content_type'],
            'path'      => $url,
        ];

        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($fileData['type'], self::$allowedMimeTypes)) {
            return false;
        }

        $file = tmpfile();

        if ($file === false) {
            return false;
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        // Put content in this file
        $path = stream_get_meta_data($file)['uri'] . uniqid() . '.' . $ext;
        file_put_contents($path, file_get_contents($url));

        return $path;
    }

    public function convertVideoMedia(VideoMedia $media)
    {
        $ffprobe = $this->container->get('dubture_ffmpeg.ffprobe');
        $ffmpeg  = $this->container->get('dubture_ffmpeg.ffmpeg');
        $galleryManager = $this->container->get('domain_business.manager.business_gallery');

        $file = $this->getLocalUrl();
        $tempFile = $this->getLocalUrl(true);
        $posterFile = $this->getLocalUrl(false, 'png');

        try {
            file_put_contents($tempFile, file_get_contents($this->getPublicUrl($media)));

            //ffmpeg script for converting video to mp4 format
            //script taken from FFMpeg Symfony Bundle https://github.com/pulse00/ffmpeg-bundle
            shell_exec('/usr/bin/ffmpeg -y -i ' . $tempFile . ' -threads 1 -vcodec libx264 -acodec ' .
                'libmp3lame -b:v 1000k -refs 6 -coder 1 -sc_threshold 40 -flags +loop -me_range 16 -subq 7 ' .
                '-i_qfactor 0.71 -qcomp 0.6 -qdiff 4 -trellis 1 -b:a 128k ' . $file);

            $score = $ffprobe->format($file)->get('probe_score');

            if ($score < $this::TRUSTED_SCORE) {
                $media->setStatus($media::VIDEO_STATUS_ERROR);

                return $media;
            }

            $video = $ffmpeg->open($file);

            $frame = $video->frame(TimeCode::fromSeconds(self::POSTER_AT_SECOND));
            $frame->save($posterFile);

            $galleryManager->createNewPosterFromLocalFile($media, $posterFile);
        } catch (\Exception $e) {
            $media->setStatus($media::VIDEO_STATUS_ERROR);

            $this->deleteLocalMediaFiles(
                [
                    $file,
                    $tempFile,
                    $posterFile,
                ]
            );

            return $media;
        }

        $fileData = [
            'name' => uniqid(),
            'type' => 'video/mp4',
            'ext' => 'mp4',
            'path' => $file,
        ];
        $uploadedFileData = $this->uploadLocalFileData($fileData);

        if ($this->filesystem->getAdapter()->exists($media->getFilepath() . $media->getFilename())) {
            $this->filesystem->delete($media->getFilepath() . $media->getFilename());
        }

        $media->setName($uploadedFileData['name']);
        $media->setFilepath($uploadedFileData['filepath']);
        $media->setFilename($uploadedFileData['filename']);
        $media->setType($uploadedFileData['type']);
        $media->setStatus($media::VIDEO_STATUS_ACTIVE);

        $this->deleteLocalMediaFiles(
            [
                $file,
                $tempFile,
                $posterFile,
            ]
        );

        return $media;
    }

    /**
     * @param VideoMedia $media
     * @param bool       $removeOldPoster
     *
     * @return bool
     */
    public function regenerateVideoPoster(VideoMedia $media, $removeOldPoster = true)
    {
        $ffmpeg  = $this->container->get('dubture_ffmpeg.ffmpeg');
        $galleryManager = $this->container->get('domain_business.manager.business_gallery');
        $status = true;

        $videoFile  = $this->getLocalUrl();
        $posterFile = $this->getLocalUrl(false, 'png');

        try {
            file_put_contents($videoFile, file_get_contents($this->getPublicUrl($media)));

            $video = $ffmpeg->open($videoFile);

            $frame = $video->frame(TimeCode::fromSeconds(self::POSTER_AT_SECOND));
            $frame->save($posterFile);

            $galleryManager->createNewPosterFromLocalFile($media, $posterFile, $removeOldPoster);
        } catch (\Exception $e) {
            $status = false;
        }

        $this->deleteLocalMediaFiles(
            [
                $videoFile,
                $posterFile,
            ]
        );

        return $status;
    }

    /**
     * @param bool $isTemp
     * @param string $extension
     *
     * @return string
     */
    public function getLocalUrl(bool $isTemp = false, $extension = 'mp4')
    {
        $path = $this->container->get('kernel')->getRootDir() . $this->container->getParameter('video_download_path');
        $name = uniqid();

        if (!file_exists($path)) {
            mkdir($path);
        }

        if ($isTemp) {
            $name = 'temp_' . $name;
        } else {
            $name .= '.' . $extension;
        }

        return $path . $name;
    }

    /**
     * @param array $files
     */
    public function deleteLocalMediaFiles(array $files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function getPublicUrl(VideoMedia $media)
    {
        $expires = new \DateTime();
        $expires->modify('+ ' . self::LINK_LIFE_TIME . ' seconds');

        $url = $this->filesystem->getAdapter()->getUrl(
            $media->getFilepath() . $media->getFilename(),
            [
                'expires' => $expires->getTimestamp(),
            ]
        );

        return $url;
    }

    protected function getExtensionByMime($type)
    {
        if (isset($this->mimeExtensions[$type])) {
            return $this->mimeExtensions[$type];
        }
        return self::DEFAULT_URI_UPLOAD_FILE_EXTENSION;
    }
    
    protected function generateFilenameForUrl($url)
    {
        $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
        if (self::MAX_FILENAME_LENGTH < strlen($domain)) {
            $domain = substr($domain, 0, 240);
        }

        $date = new \DateTime();
        $domain .= '-' . $date->format('YmdHis');
        return $domain;
    }
}
