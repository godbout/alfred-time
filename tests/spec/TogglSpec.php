<?php

namespace spec\AlfredTime;

use AlfredTime\Toggl;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TogglSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Toggl::class);
    }

    function it_returns_empty_array_when_get_projects_is_given_empty_haystack()
    {
        $this->getProjects([])->shouldReturn([]);
    }
}
