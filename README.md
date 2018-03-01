Install

```
composer require tobur/vich-uploader-encryp v0.1
```

```
VichUploaderEncryp\VichUploaderEncrypBundle::class => ['all' => true],
```


Create vich_uploader_encryp.yaml for configure bundle:
```
vich_uploader_encryp:
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
