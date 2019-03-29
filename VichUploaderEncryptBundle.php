<?php

namespace SfCod\VichUploaderEncrypt;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SfCod\VichUploaderEncrypt\DependencyInjection\VichUploaderEncryptExtension;

class VichUploaderEncryptBundle extends Bundle
{
    /**
     * @return null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|VichUploaderEncryptExtension
     */
    public function getContainerExtension()
    {
        return new VichUploaderEncryptExtension();
    }
}