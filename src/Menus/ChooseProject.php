<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class ChooseProject extends Menu
{
    public static function content(): array
    {
        $projects =  [
            self::getNoProject()
        ];

        $projects += self::getServiceProjects(Workflow::serviceEnabled());

        return $projects;
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
                ->variable('project_id', $id)
                ->arg('choose_tag')
                ->icon(Icon::create("resources/icons/$service.png"));
        }

        return $projects;
    }
}
