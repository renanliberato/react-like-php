<?php

namespace App\Reducers;

class AppReducer
{
    public function __invoke($state, $action)
    {
        switch ($action['type']) {
            case 'ADD_TODO':
                $state['todos'][] = ['id' => count($state['todos']) + 1, 'name' => $action['name'], 'completed' => false];
                return $state;
            case 'TOGGLE_TODO':
                foreach ($state['todos'] as $key => $todo) {
                    if ($todo['id'] == $action['id']) {
                        $state['todos'][$key]['completed'] = $action['completed'];
                    }
                }
                return $state;
            case 'REMOVE_TODO':
                $state['todos'] = array_filter($state['todos'], function ($i) use ($action) {
                    return $i['id'] != $action['id'];
                });
                return $state;
            case 'EDIT_TODO_SHOW':
                $state['ui']['editing_todo'] = $action['id'];
                return $state;
            case 'EDIT_TODO_SAVE':
                foreach ($state['todos'] as $key => $todo) {
                    if ($todo['id'] == $action['id']) {
                        $state['todos'][$key]['name'] = $action['name'];
                    }
                }
                $state['ui']['editing_todo'] = false;

                return $state;
            default:
                return $state;
        }
    }
}
