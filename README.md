## Installation

Install via composer:
```
composer require sfcod/vich-uploader-encrypt
```

Add bundle to bundles.php:
```
return [
    ...
    SfCod\VichUploaderEncrypt\VichUploaderEncryptBundle::class => ['all' => true],
    ...
];
```


Create sfcod_vich_uploader_encrypt.yaml config:
```
sfcod_vich_uploader_encrypt:
  encryption_key: '%env(ENC_U_KEY)%' # key for encryption (string)
  encryption_iv: '%env(ENC_U_IV)%' # initialization vector for encryption (string)
```

## Usage:

```
use SfCod\VichUploaderEncrypt\VichUploader\Mapping\UploadableField;
...

/*
* @UploadableField(mapping="cv_file", fileNameProperty="cvName", encrypted=true)
*/
```
