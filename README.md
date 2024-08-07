# ThinkPHP flysystem package

This is a filesystem extension package for ThinkPHP 8.0, supporting local file storage, Qiniu OSS storage, and Aliyun OSS storage (Aliyun). This package seamlessly integrates with ThinkPHP, providing a robust and flexible solution for managing file storage across multiple storage backends. With this package, developers can easily switch between different storage options, ensuring efficient and reliable file handling in their applications. Future versions of this package will include support for additional storage systems, further enhancing its versatility and functionality.

## Requirement

- `PHP` >= 8.2
- `league/flysystem` ^3.28

## Installation

```shell
$ composer require libratechie/think-filesystem
```

## Usage

### Method 1: Using Direct Class Reference
You can directly reference the Filesystem class provided by the package. This approach allows you to use the disk method to specify which disk you want to work with:
```php
use \Libratechie\Think\Filesystem;
$disk = Filesystem::disk('public');
```

### Method 2: Using ThinkPHP Container Resolution
Alternatively, you can leverage ThinkPHP's container to resolve the filesystem service. This method is useful when you want to adhere to dependency injection principles and make your code more testable:
```php
$disk = app('filesystem')::disk('qiniu');
```

## Configuration

### Local

```php
return [
    'disks'   => [
        //...
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'public/storage',
            'url'        => '/storage',
            'visibility' => 'public',
        ],
        //...
    ],
];
```

### Aliyun

```php
return [
    'disks'   => [
        //...
        'aliyun' => [
            'type'            => 'aliyun',
            'accessKeyId'     => '<aliyun access id>',
            'accessKeySecret' => '<aliyun access secret>',
            'bucket'          => '<bucket name>',
            'endpoint'        => '<endpoint address>',
            // 'domain'       => 'bucket.oss-cn-guangzhou.aliyuncs.com',
            // or with protocol: https://bucket.oss-cn-guangzhou.aliyuncs.com
        ]
        //...
    ],
];
```
> Domain Configuration： The domain configuration specifies the external access path for your Alibaba Cloud OSS resources. By default, using the OSS default domain to access HTML or image resources may result in them being downloaded as attachments. To allow direct access via a web browser, you need to use a custom domain.

### Qiniu

```php
return [
    'disks'   => [
        //...
        'qiniu'  => [
            'type'      => 'qiniu',
            'accessKey' => '<qiniu access key>',
            'secretKey' => '<qiniu secret key>',
            'bucket'    => '<bucket name>',
            'domain'    => 'xxxxx.hn-bkt.clouddn.com',
            // or with protocol: https://xxxxx.hn-bkt.clouddn.com
        ],
        //...
    ],
];
```

## API

```php
// Write files
$folderPath = '/path/to';
$file = request()->file('file');

// Use the default naming convention to write the file
$fileName = $disk->putFile($folderPath, $file);
// $fileName: /path/to/20240725/2697c763c84fe48d0166d0cd37181e19.jpg

// Use SHA-256 hash as the file name
$fileName = $disk->putFile($folderPath, $file, 'sha256');
// $fileName: /path/to/55/fd6b615cb02ce73c8e708ac62c9fe9c0cdd92d9161c57186e592d2b672e6e3.jpg

// Use a custom callback function to generate the file name
$fileName = $disk->putFile($folderPath, $file, function ($fileHash) {
    return 'custom' . DIRECTORY_SEPARATOR . md5($fileHash->getPathname());
});
// $fileName: /path/to/custom/db8fbf2c977c3fae6276521f788d5183.jpg
```

```php
// Save the file with a specified file name
$fileName = $disk->putFileAs($folderPath, $file, 'custom.txt');
```

```php
// Check whether a file exists.
$exists = $disk->fileExists('/path/to/custom.txt');
```

```php
// Get file access path.
$exists = $disk->url('/path/to/custom.txt');
```

```php
// Get file size.
$size = $disk->fileSize('/path/to/custom.txt');
```

```php
// Get file mimeType.
$size = $disk->mimeType('/path/to/custom.txt');
```

```php
// Copy files
$disk->copy('/path/to/file.txt', '/path/to/copy_file.txt');
```

```php
// Move files
$disk->move('/path/to/custom.txt', '/path/to/moved_file.txt');
```

```php
// Delete files
$disk->delete('/path/to/file.txt');
```