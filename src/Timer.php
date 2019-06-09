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
        return Workflow::serviceEnabled()->stopCurrentTimer();
    }

    public static function continue($timerId)
    {
        return Workflow::serviceEnabled()->continueTimer($timerId);
    }
}
