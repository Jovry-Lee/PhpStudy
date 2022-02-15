<?php

namespace CommandPattern\Command\CellingFan;

use CommandPattern\Command\Command;
use CommandPattern\Receiver\CellingFan;

class CellingFanOnCommand implements Command
{
    /**@var CellingFan $cellingFan*/
    protected $cellingFan;

    public function __construct(CellingFan $cellingFan)
    {
        $this->cellingFan = $cellingFan;
    }

    public function execute()
    {
        $this->cellingFan->high();
    }

    public function undo()
    {
        $this->cellingFan->off();
    }
}