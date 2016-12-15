<?php

namespace Oxa\VideoBundle\Manager;

use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Gaufrette\Filesystem;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Uploader\VideoLocalFileUploader;
use Oxa\VideoBundle\Uploader\VideoRemoteFileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoManager
{
    private static $allowedMimeTypes = array(
        'video/mp4',
        'video/webm',
    );

    private $filesystem;
    private $videoLocalFileUploader;
    private $videoRemoteFileUploader;

    private $videoMediaManager;

    public function __construct(
        Filesystem $filesystem,
        VideoMediaManager $videoMediaManager
    ) {
        $this->filesystem = $filesystem;
        $this->videoMediaManager = $videoMediaManager;
    }

    public function removeMedia($id)
    {
        $media = $this->videoMediaManager->find($id);

        if ($this->filesystem->getAdapter()->exists($media->getFilepath().$media->getFilename())) {
            return $this->filesystem->delete($media->getFilepath().$media->getFilename());
        }

        return true;
    }

    public function uploadLocalFile(UploadedFile $file, array $data = []) : VideoMedia
    {
        $uploadedFileData = $this->uploadLocalFileData($file, $data);

        return $this->videoMediaManager->save($uploadedFileData);
    }

    public function uploadLocalFileData(UploadedFile $file, array $data = []) : array
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.', $file->getClientMimeType()));
        }

        $adapter = $this->filesystem->getAdapter();

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', 1), $file->getClientOriginalExtension());
        } while ($adapter->exists($path.$filename));
 
        $adapter->setMetadata($path.$filename, [
            'contentType'   => $file->getClientMimeType(),
            'ACL'           => 'public-read',
        ]);
        $uploadedSize = $adapter->write($path.$filename, file_get_contents($file->getPathname()));

        if (!$uploadedSize) {
            throw new \InvalidArgumentException(sprintf('File '.$filename.' is not uploaded. Please contact administrator'));
        }

        $video = [
            'name'      => $file->getClientOriginalName(),
            'filename'  => $filename,
            'type'      => $file->getClientMimeType(),
            'filepath'  => $path,
        ];

        return $video;
    }

    public function uploadRemoteFile(string $url, array $data = [])
    {
        $headers = SiteHelper::checkUrlExistence($url);

        if ($headers && in_array($headers['content_type'], SiteHelper::$videoContentTypes)) {
            $uploaderRequestData = ['url' => $url];
            $fileUploader = $this->videoRemoteFileUploader->setData(array_merge($uploaderRequestData, $data));

            $videoMediaData = $fileUploader->upload();

            return $this->videoMediaManager->save($videoMediaData);
        }

        return false;
    }

    public function getEmbedCode(string $hash, array $dimensions = [])
    {
        return $this->videoEmbedAPIManager->get($hash, $dimensions);
    }
}
