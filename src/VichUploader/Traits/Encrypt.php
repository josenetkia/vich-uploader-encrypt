<?php

namespace VichUploaderEncrypt\VichUploader\Traits;

use Vich\UploaderBundle\Mapping\PropertyMapping;

trait Encrypt
{
    /**
     * @param PropertyMapping $propertyMapping
     * @return bool
     */
    public function isEncryptFile(PropertyMapping $propertyMapping): bool
    {
        $reflectionClass = new \ReflectionClass($propertyMapping);
        $propertyPaths = $reflectionClass->getProperty('propertyPaths');
        $propertyPaths->setAccessible(true);
        $data = $propertyPaths->getValue($propertyMapping);

        if (!isset($data['encrypted'])) {
            return false;
        }

        return $data['encrypted'] === true;
    }

}