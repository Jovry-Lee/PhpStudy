<?php

namespace IocPattern\LaravelContainer\Test;

use IocPattern\LaravelContainer\Test\Traffic\Visit;

class Traveller
{
    protected $trafficTool;
    public function __construct(Visit $trafficTool)
    {
        $this->trafficTool = $trafficTool;
    }

    public function visitTibet()
    {
        $this->trafficTool->go();
    }
}