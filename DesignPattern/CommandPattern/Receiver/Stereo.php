<?php

/**
 * 音响。
 */
namespace CommandPattern\Receiver;


class Stereo
{
    public $location;

    public function __construct($location = '')
    {
        $this->location = $location;
    }

    public function on()
    {
        echo "{$this->location} Stereo is on.\n";
    }

    public function off()
    {
        echo "{$this->location} Stereo is off.\n";
    }

    public function setCd($cdName)
    {
        echo "{$this->location} Stereo set cd {$cdName}.\n";
    }

    public function setDvd($dvdName)
    {
        echo "{$this->location} Stereo set Dvd {$dvdName}.\n";
    }

    public function setRadio($radioName)
    {
        echo "{$this->location} Stereo set redio {$radioName}.\n";
    }

    public function setVolume($volume)
    {
        echo "{$this->location} Stereo set volume {$volume}.\n";
    }
}