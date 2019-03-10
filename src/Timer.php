<?php

namespace Godbout\Alfred\Time;

class Timer
{
    public static function start()
    {
        $toggl = new Toggl(Workflow::getConfig()->read('toggl.api_token'));

        return (bool) $toggl->startTimer();
    }

    public static function running()
    {
        $toggl = new Toggl(Workflow::getConfig()->read('toggl.api_token'));

        return (bool) $toggl->runningTimer();
    }

    public static function stop()
    {
        $toggl = new Toggl(Workflow::getConfig()->read('toggl.api_token'));

        return (bool) $toggl->stopCurrentTimer();
    }
}
