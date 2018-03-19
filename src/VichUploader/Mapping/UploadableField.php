<?php

namespace VichUploaderEncrypt\VichUploader\Mapping;

use Vich\UploaderBundle\Mapping\Annotation\UploadableField as BaseUploadableField;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class UploadableField extends BaseUploadableField
{
    /**
     * @var bool
     */
    protected $encrypted;

    /**
     * @return bool
     */
    public function getEncrypted(): bool
    {
        return (bool) $this->encrypted;
    }
}