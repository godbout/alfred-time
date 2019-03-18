<?php

namespace Godbout\Alfred\Time;

abstract class TimerService
{
    abstract public function runningTimer();

    public function __toString()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }
}
