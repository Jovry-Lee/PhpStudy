<?php

/**
 * 吊扇。
 */
namespace CommandPattern\Receiver;

class CellingFan
{
    public $location;
    public $speed;

    public function __construct($location = '')
    {
        $this->location = $location;
        $this->speed = 'medium';
    }

    public function high()
    {
        $this->speed = 'high';
        echo "{$this->location} Stereo is high.\n";
    }

    public function medium()
    {
        $this->speed = 'medium';
        echo "{$this->location} Stereo is medium.\n";
    }

    public function low()
    {
        $this->speed = 'low';
        echo "{$this->location} Stereo is low.\n";
    }

    public function off()
    {
        echo "{$this->location} Stereo is off.\n";
    }

    public function getSpeed()
    {
        return $this->speed;
    }
}