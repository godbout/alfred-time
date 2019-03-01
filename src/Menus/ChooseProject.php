<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class ChooseProject extends Menu
{
    public static function content(): array
    {
        $projects = Workflow::serviceEnabled()->projects();

        $noProject[] = Item::create()
            ->title('No project')
            ->subtitle('Timer will be created without a project')
            ->arg('no_project');

        return $noProject + $projects;
    }
}
