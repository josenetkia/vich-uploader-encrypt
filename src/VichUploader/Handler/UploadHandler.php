<?php

namespace VichUploaderEncryp\Cryptography\VichUploader\Handler;

use VichUploaderEncryp\Cryptography\VichUploader\Traits\Encrypt;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vich\UploaderBundle\Handler\UploadHandler as BaseHandler;
use VichUploaderEncryp\Cryptography\Crypt\Encryption;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Injector\FileInjectorInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

class UploadHandler extends BaseHandler
{
    use Encrypt;

    /**
     * @var Encryption
     */
    protected $encryption;

    /**
     * @param PropertyMappingFactory $factory
     * @param StorageInterface $storage
     * @param FileInjectorInterface $injector
     * @param EventDispatcherInterface $dispatcher
     * @param Encryption $encryption
     */
    public function __construct(
        PropertyMappingFactory $factory,
        StorageInterface $storage,
        FileInjectorInterface $injector,
        EventDispatcherInterface $dispatcher,
        Encryption $encryption
    ) {
        parent::__construct($factory, $storage, $injector, $dispatcher);
        $this->encryption = $encryption;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($obj, string $fieldName): void
    {
        $mapping = $this->getMapping($obj, $fieldName);
        if (!$this->isEncryptFile($mapping)) {
            parent::upload($obj, $fieldName);

            return;
        }

        if (!$this->hasUploadedFile($obj, $mapping)) {
            return;
        }

        $this->dispatch(Events::PRE_UPLOAD, new Event($obj, $mapping));
        $fileRealPath = $mapping->getFile($obj)->getRealPath();
        file_put_contents($fileRealPath, $this->encryption->encrypt(file_get_contents($fileRealPath)));
        $this->storage->upload($obj, $mapping);
        $this->injector->injectFile($obj, $mapping);

        $this->dispatch(Events::POST_UPLOAD, new Event($obj, $mapping));
    }
}