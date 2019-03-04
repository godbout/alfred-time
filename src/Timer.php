<?php

namespace Godbout\Alfred\Time;

use Godbout\Alfred\Time\Toggl;

class Timer
{
    public static function start()
    {
        $toggl = new Toggl(Workflow::getConfig()->read('toggl.api_token'));

        return $toggl->startTimer();
    }
}
