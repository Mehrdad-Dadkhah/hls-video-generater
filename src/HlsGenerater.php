<?php

namespace MehrdadDadkhah\Video\HlsGenerater;

use FFMpeg\FFProbe;
use League\Flysystem\Plugin\ListFiles;
use MehrdadDadkhah\Flysystem\Tempdir;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Generate video m3u8 playlists
 */
class HlsGenerater
{

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var string
     */
    private $prefix = 'sprite';

    /**
     * @var string
     */
    private $urlPrefix = '';

    /**
     * @var string
     */
    private $converter = 'ffmpeg';

    /**
     * @return string
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * @param string $converter
     * @throws RuntimeException
     */
    public function setConverter($converter)
    {
        $convertersWhitelist = [
            'ffmpeg',
        ];

        if (!file_exists($source)) {
            throw new RuntimeException(sprintf("converter libarary %s is not supported! please select ffmpeg.", $converter));
        }

        $this->source = $converter;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @throws RuntimeException
     */
    public function setSource($source)
    {
        if (!file_exists($source)) {
            throw new RuntimeException(sprintf("source video file %s not found", $source));
        }

        $this->source = $source;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    /**
     * @param mixed $outputDirectory
     */
    public function setOutputDirectory($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
    }

    /**
     * Generates m3u8
     *
     * @throws \Exception
     */
    public function generate()
    {
        // create temporay directory
        $tempDir = new Tempdir('sprite');
        $tempDir->addPlugin(new ListFiles);

        $generateCommand = $this->getGenerateM3u8Command();

        $qualities = $this->getVideoQualities();

        $mainFileData = "#EXTM3U\n";

        foreach ($qualities as $quality) {
            $bitrate          = $this->getRelatedBitrate($quality);
            $playListPath     = $this->getOutputDirectory() . '/' . $this->getPrefix() . '_' . $quality . '_manifest.m3u8';
            $playListTempPath = $tempDir->getPath() . '/' . $this->getPrefix() . '_' . $quality . '_manifest.m3u8';

            $cmd = sprintf($generateCommand,
                $this->getSource(),
                $quality,
                $bitrate,
                $playListTempPath
            );

            $proc = new Process($cmd);
            $proc->setTimeout(null);
            $proc->run();

            if (!$proc->isSuccessful()) {
                throw new \RuntimeException($cmd . ": " . $proc->getErrorOutput());
            }

            $mainFileData .= $this->getHlsManifestData($bitrate);
            $mainFileData .= $playListPath;
        }

        $finalFile = fopen($tempDir->getPath() . '/' . $this->getPrefix() . '_hls_manifest.m3u8', "w") or die("Unable to open file!");
        fwrite($finalFile, $data);
        fclose($finalFile);

        $moveCommand = sprintf(
            'mv %s/* %s/',
            $tempDir->getPath(),
            $this->getOutputDirectory()
        );

        $proc = new Process();
        $proc->setTimeout(null);
        $proc->run();

        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($moveCommand . ": " . $proc->getErrorOutput());
        }

        return [
            'finalManifestFile' => $finalFile,
        ];
    }

    /**
     * return generate m3u8 hls from video command base on selected converter lib
     *
     * @return  string [generate command]
     */
    private function getGenerateM3u8Command()
    {
        return 'ffmpeg -y -ss %d -i %s -frames:v 1 -filter:v scale=%d:-1 %s/%04d.jpg';
    }

    private function getVideoQualities()
    {
        // get basic info about video
        $ffprobe     = FFProbe::create()->format($this->getSource());
        $videoHeight = $ffprobe->get('height');

        $videoHeights = [
            144,
            240,
            360,
            480,
            720,
            1080,
            1440,
            2160,
        ];

        $heights = array_filter(
            $videoHeights,
            function ($value) use ($videoHeight) {
                return ($value >= $videoHeight);
            }
        );

        return $heights;
    }

    private function getRelatedBitrate(int $quality)
    {
        $qualitiesBitrate = [
            1440 => 2500,
            1080 => 2000,
            720  => 950,
            480  => 700,
            360  => 550,
            240  => 350,
            144  => 190,
        ];

        return $qualitiesBitrate[$quality];
    }
}
