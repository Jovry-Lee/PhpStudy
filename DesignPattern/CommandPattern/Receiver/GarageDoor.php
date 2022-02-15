<?php

namespace CommandPattern\Receiver;

/**
 * 车库门。
 */
class GarageDoor
{
    public function up()
    {
        echo "Garage door is up!\n";
    }

    public function down()
    {
        echo "Garage door is down!\n";
    }

    public function stop()
    {
        echo "Garage door is stop!\n";
    }

    public function lightOn()
    {
        echo "Garage door is light on!\n";
    }

    public function lightOff()
    {
        echo "Garage door light off!\n";
    }
}