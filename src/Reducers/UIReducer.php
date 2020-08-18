<?php

namespace App\Reducers;

class UIReducer
{
    public function __invoke($state, $action)
    {
        switch ($action['type']) {
            case 'EDIT_TODO_SHOW':
                $state['editing_todo'] = $action['id'];
                return $state;
            case 'EDIT_TODO_SAVE':
                $state['ui']['editing_todo'] = false;

                return $state;
            default:
                return $state;
        }
    }
}
