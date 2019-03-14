<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class ChooseProject extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(self::getNoProject());

        foreach (self::getServiceProjects(Workflow::serviceEnabled()) as $project) {
            ScriptFilter::add($project);
        }

        if (self::userInput()) {
            ScriptFilter::filterItems(self::userInput());
        }
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
                ->variable('timer_project', $id)
                ->arg('choose_tag')
                ->icon(Icon::create("resources/icons/$service.png"));
        }

        return $projects;
    }
}
