<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class ChooseProject extends Menu
{
    public static function content(): array
    {
        return array_merge(
            [self::getNoProject()],
            self::getServiceProjects(Workflow::serviceEnabled())
        );
    }

    private static function getNoProject()
    {
        return Item::create()
            ->title('No project')
            ->subtitle('Timer will be created without a project')
            ->arg('choose_tag');
    }

    private static function getServiceProjects($service)
    {
        $projects = [];

        foreach ($service->projects() as $id => $name) {
            $projects[] = Item::create()
                ->title($name)
                ->subtitle($id)
                ->variable('timer_project', $id)
                ->arg('choose_tag')
                ->icon(Icon::create("resources/icons/$service.png"));
        }

        return $projects;
    }
}
