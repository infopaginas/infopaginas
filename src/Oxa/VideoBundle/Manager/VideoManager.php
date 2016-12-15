<?php

namespace Oxa\VideoBundle\Manager;

use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Gaufrette\Filesystem;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoManager
{
    private static $allowedMimeTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
    ];
    
    private $mimeExtensions = [
        'video/mp4'     => '.mp4',
        'video/webm'    => '.webm',
        'video/ogg'     => '.ogv',
    ];

    private $filesystem;

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
        $fileData = [
            'name'      => $file->getClientOriginalName(),
            'type'      => $file->getClientMimeType(),
            'ext'       => $file->getClientOriginalExtension(),
            'path'      => $file->getPathname(),
        ];

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        return $this->videoMediaManager->save($uploadedFileData);
    }

    public function uploadLocalFileData(array $data) : array
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($data['type'], self::$allowedMimeTypes)) {
            throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.', $data['type']));
        }

        $adapter = $this->filesystem->getAdapter();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', 1), $data['ext']);
        } while ($adapter->exists($path.$filename));
 
        $adapter->setMetadata($path.$filename, [
            'contentType'   => $data['type'],
            'ACL'           => 'public-read',
        ]);
        $uploadedSize = $adapter->write($path.$filename, file_get_contents($data['path']));

        if (!$uploadedSize) {
            throw new \InvalidArgumentException(sprintf('File '.$filename.' is not uploaded. Please contact administrator'));
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

        $uploadedFileData = $this->uploadLocalFileData($fileData);

        return $this->videoMediaManager->save($uploadedFileData);
    }

    protected function getExtensionByMime(string $type)
    {
        if (isset($this->mimeExtensions[$type])) {
            return $this->mimeExtensions[$type];
        }
        return '.mp4';
    }
    
    protected function generateFilenameForUrl($url)
    {
        $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
        if (240 < strlen($domain)) {
            $domain = substr($domain, 0, 240);
        }

        $date = new \DateTime();
        $domain .= '-'.$date->format('YmdHis');
        return $domain;
    }
}
