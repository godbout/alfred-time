<?php

unset($data['continue']);

switch ($data['action']) {
    case 'setup':
    case 'config':
    case 'sync':
    case 'undo':
    case 'stop':
    case 'final':
        $data['continue'] = false;
        break;

    case 'delete':
    case 'continue':
        $data['original_action'] = $data['action'];
        $data['show'] = 'timer_list';
        $data['continue'] = true;
        break;

    case 'choose_timer':
        $data['action'] = 'final';
        $data['continue'] = false;
        break;

    case 'choose_project':
        $data['show'] = 'tag_list';
        $data['continue'] = true;
        break;

    case 'choose_tag':
        $data['action'] = 'final';
        $data['continue'] = false;
        break;

    case 'start':
    case 'start_all':
        $data['original_action'] = $data['action'];
        $data['show'] = 'project_list';
        $data['continue'] = true;
        break;

    default:
        break;
}

echo json_encode($data);
