Install

```
composer require tobur/vich-uploader-encrypt v0.1
```

```
VichUploaderEncryp\VichUploaderEncryptBundle::class => ['all' => true],
```


Create vich_uploader_encryp.yaml for configure bundle:
```
vich_uploader_encrypt:
      encryption_key: some key for encrypt
      encryption_vi: some vi for encrypt
```
Basic Usage:

```
* @UploadableField( 
* mapping="cv_file", 
* fileNameProperty="cvName",
* encrypted=true 
* )
```
