<?php

use MehrdadDadkhah\Video\HlsGenerater;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class HlsGeneraterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Filesystem
     */
    public $outputFS;
    public $testSrc = __DIR__ . '/test_data/bbb_sunflower_1080p_30fps_normal.mp4';
    public $testSrcUrl = 'https://video.labs.gameswelt.de/big-bucks-bunny/bbb_sunflower_1080p_30fps_normal.mp4';

}
