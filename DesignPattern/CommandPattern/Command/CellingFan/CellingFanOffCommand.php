<?php

namespace CommandPattern\Command\CellingFan;

use CommandPattern\Command\Command;
use CommandPattern\Receiver\CellingFan;

/**
 * 关闭吊扇。
 */
class CellingFanOffCommand implements Command
{
    /**@var CellingFan $cellingFan*/
    protected $cellingFan;

    public function __construct(CellingFan $cellingFan)
    {
        $this->cellingFan = $cellingFan;
    }

    public function execute()
    {
        $this->cellingFan->off();
    }

    public function undo()
    {
        $this->cellingFan->high();
    }
}