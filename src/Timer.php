<?php

namespace Godbout\Alfred\Time;

class Timer
{
    public static function start()
    {
        return Workflow::serviceEnabled()->startTimer();
    }

    public static function running()
    {
        return Workflow::serviceEnabled()->runningTimer();
    }

    public static function stop()
    {
        return WOrkflow::serviceEnabled()->stopCurrentTimer();
    }
}
