<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Item;

class ChooseProject extends Menu
{
    public static function content(): array
    {
        $projects = [];



        $noProject[] = Item::create()
            ->title('No project')
            ->subtitle('Timer will be created without a project')
            ->arg('no_project');


        /**
         * iTodo
         *
         * - Write a test for the real list of projects
         */
        foreach (Workflow::serviceEnabled()->projects() as $project) {
            $projects[] = Item::create()
                ->title($project[1])
                ->variable('project_id', $project[0]);
        }

        return $noProject + $projects;
    }
}
