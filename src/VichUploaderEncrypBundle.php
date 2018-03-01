<?php

namespace VichUploaderEncryp;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use VichUploaderEncryp\DependencyInjection\VichUploaderEncryptExtension;

class VichUploaderEncrypBundle extends Bundle
{
    /**
     * @return null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|VichUploaderEncryptExtension
     */
    public function getContainerExtension()
    {
        return new VichUploaderEncryptExtension();
    }
}