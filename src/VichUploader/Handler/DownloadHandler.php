<?php

namespace VichUploaderEncryp\Cryptography\VichUploader\Handler;

use VichUploaderEncryp\Cryptography\VichUploader\Traits\Encrypt;
use Vich\UploaderBundle\Handler\DownloadHandler as BaseHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use VichUploaderEncryp\Cryptography\Crypt\Encryption;
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
    ) {
        parent::__construct($factory, $storage);
        $this->encryption = $encryption;
    }

    /**
     * Create a response object that will trigger the download of a file.
     *
     * @param object|array $object
     * @param string       $field
     * @param string       $className
     * @param string|bool  $fileName  True to return original file name
     *
     * @return StreamedResponse
     *
     * @throws \Vich\UploaderBundle\Exception\MappingNotFoundException
     * @throws NoFileFoundException
     * @throws \InvalidArgumentException
     */
    public function downloadObject(
        $object,
        string $field,
        ?string $className = null,
        $fileName = false
    ): StreamedResponse {
        $mapping = $this->getMapping($object, $field, $className);
        if (!$this->isEncryptFile($mapping)) {
            return parent::downloadObject($object, $field, $className, $fileName);
        }

        if (true === $fileName) {
            $fileName = $mapping->readProperty($object, 'originalName');
        }

        $responseFileName = !empty($fileName) ? $fileName : $mapping->getFileName($object);
        $realPath = $mapping->getUploadDestination() . DIRECTORY_SEPARATOR . $mapping->getFileName($object);

        if (!file_exists($realPath)) {
            throw new NoFileFoundException(sprintf('No file found in field "%s".', $field));
        }

        return $this->createResponse($realPath, $responseFileName, $mapping->getFile($object));
    }

    /**
     * @param string $realPath
     * @param string $fileName
     * @param null|File $file
     * @return StreamedResponse
     */
    protected function createResponse(string $realPath, string $fileName, ?File $file): StreamedResponse
    {
        $newRealPath = tempnam(sys_get_temp_dir(), 'download_');
        file_put_contents($newRealPath, $this->encryption->decrypt(file_get_contents($realPath)));
        $mimeType = $file ? $file->getMimeType() : null;
        $handle = fopen($newRealPath, 'rb');

        $response = new StreamedResponse(function() use ($handle) {
            stream_copy_to_stream($handle, fopen('php://output', 'wb'));
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            Transliterator::transliterate($fileName)
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $mimeType ?? 'application/octet-stream');

        return $response;
    }
}