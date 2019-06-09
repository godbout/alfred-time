<?php

namespace Godbout\Alfred\Time\Services;

abstract class TimerService
{
    public $allowsEmptyProject = true;

    public $allowsEmptyTag = true;

    abstract public function startTimer();

    abstract public function runningTimer();

    abstract public function stopCurrentTimer();

    abstract public function continueTimer($timerId);

    abstract public function deleteTimer($timerId);

    abstract public function projects();

    abstract public function tags();

    abstract public function pastTimers();

    public function __toString()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }
}
