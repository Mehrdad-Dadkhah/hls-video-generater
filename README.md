# HlsVideoGenerater

[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/Mehrdad-Dadkhah/hls-video-generater.svg?style=flat-square)](https://packagist.org/packages/Mehrdad-Dadkhah/HlsVideoGenerater)

PHP package for generating video m3u8 playlist. it generate hls video for diffrent bitrates.

## System requirements

Tested with >=5.5, following binaries need to be installed

* [ffmpeg](http://www.ffmpeg.org/download.html) (tested with v2.2)

## Installation

```
composer require mehrdad-dadkhah/hls-video-generater
```

## Usage

```PHP
use MehrdadDadkhah\Video\HlsGenerater;

$hlsGenerater = new HlsGenerater();
$result = $hlsGenerater->setSource('path-to-video')
            ->setOutputDirectory('path-to-output-directory')
            ->checkAndGenerateOutputDirectory()
            ->setUri('/example/uri')
            ->setPrefix('sprite')
            ->setFilesOwnerAndPermission('root:www-data', 775)
            ->generate();
```

## Acknowledgments

* Thanks to [emgag](https://github.com/emgag) I use his package and follow [video-thumbnail-sprite](https://github.com/emgag/video-thumbnail-sprite) structure.

Uses:

* [emgag/flysystem-tempdir](https://github.com/emgag/flysystem-tempdir)
* [symfony/process](https://github.com/symfony/Process)

## License

hls-video-generater is licensed under the [GPLv3 License](http://opensource.org/licenses/GPL).