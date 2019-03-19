<?php

namespace Godbout\Alfred\Time;

abstract class TimerService
{
    public $allowsEmptyProject = true;

    public $allowsEmptyTag = true;

    abstract public function startTimer();

    abstract public function runningTimer();

    abstract public function stopCurrentTimer();

    abstract public function projects();

    abstract public function tags();

    public function __toString()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }
}
