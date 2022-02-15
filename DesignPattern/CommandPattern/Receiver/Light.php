<?php

/**
 * 灯。
 */
namespace CommandPattern\Receiver;

class Light
{
    public $location;

    public function __construct($location = '')
    {
        $this->location = $location;
    }

    public function on()
    {
        // todo 实现开灯.
        echo "{$this->location} Light is On\n";
    }

    public function off()
    {
        // todo 实现关灯.
        echo "{$this->location} Light is Off\n";
    }
}