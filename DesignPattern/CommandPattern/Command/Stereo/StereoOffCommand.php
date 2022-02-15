<?php

namespace CommandPattern\Command\Stereo;

use CommandPattern\Command\Command;
use CommandPattern\Receiver\Stereo;

class StereoOffCommand implements Command
{
    /**@var Stereo $stereo*/
    protected $stereo;

    public function __construct(Stereo $stereo)
    {
        $this->stereo = $stereo;
    }

    public function execute()
    {
        $this->stereo->off();
    }

    public function undo()
    {
        $this->stereo->on();
        $this->stereo->setCd('Jordon');
        $this->stereo->setVolume(11);
    }
}