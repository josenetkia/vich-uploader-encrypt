<?php

namespace SfCod\VichUploaderEncrypt\VichUploader\Handler;

use SfCod\VichUploaderEncrypt\VichUploader\Traits\Encrypt;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Vich\UploaderBundle\Handler\DownloadHandler as BaseHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use SfCod\VichUploaderEncrypt\Crypt\Encryption;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Util\Transliterator;
use Symfony\Component\HttpFoundation\File\File;

class DownloadHandler extends BaseHandler
{
    use Encrypt;

    /**
     * @var Encryption
     */
    protected $encryption;

    /**
     * @param PropertyMappingFactory $factory
     * @param StorageInterface $storage
     * @param Encryption $encryption
     */
    public function __construct(
        PropertyMappingFactory $factory,
        StorageInterface $storage,
        Encryption $encryption
    )
    {
        parent::__construct($factory, $storage);
        $this->encryption = $encryption;
    }

    /**
     * Create a response object that will trigger the download of a file.
     *
     * @param object|array $object
     * @param string $field
     * @param string $className
     * @param string|bool $fileName True to return original file name
     *
     * @return StreamedResponse
     *
     * @throws \Vich\UploaderBundle\Exception\MappingNotFoundException
     * @throws NoFileFoundException
     * @throws \InvalidArgumentException
     */
    public function downloadObject($object, string $field, ?string $className = null, $fileName = null, bool $forceDownload = true): StreamedResponse
    {
        $mapping = $this->getMapping($object, $field, $className);
        if (!$this->isEncryptFile($mapping)) {
            return parent::downloadObject($object, $field, $className, $fileName, $forceDownload);
        }

        if (true === $fileName) {
            $fileName = $mapping->readProperty($object, 'originalName');
        }

        $responseFileName = !empty($fileName) ? $fileName : $mapping->getFileName($object);
        if ($mapping->getDirectoryNamer()) {
            $realPath = $mapping->getUploadDestination() . DIRECTORY_SEPARATOR . $mapping->getDirectoryNamer()->directoryName($object, $mapping) . DIRECTORY_SEPARATOR . $mapping->getFileName($object);
        } else {
            $realPath = $mapping->getUploadDestination() . DIRECTORY_SEPARATOR . $mapping->getFileName($object);
        }

        if (!file_exists($realPath)) {
            throw new NoFileFoundException(sprintf('No file found in field "%s".', $field));
        }

        return $this->createResponse($realPath, $responseFileName, $forceDownload);
    }

    /**
     * Create streamed response
     *
     * @param string $realPath
     * @param string $fileName
     * @param null|File $file
     *
     * @return StreamedResponse
     */
    protected function createResponse(string $realPath, string $fileName, bool $forceDownload = true): StreamedResponse
    {
        $newRealPath = tempnam(sys_get_temp_dir(), 'download_');
        file_put_contents($newRealPath, $this->encryption->decrypt(file_get_contents($realPath)));

        $file = new File($newRealPath, false);
        $mimeType = $file ? $file->getMimeType() : null;
        $handle = fopen($newRealPath, 'rb');

        $response = new StreamedResponse(function () use ($handle) {
            stream_copy_to_stream($handle, fopen('php://output', 'wb'));
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            Transliterator::transliterate($fileName)
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $mimeType ?? 'application/octet-stream');

        return $response;
    }
}