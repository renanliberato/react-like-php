<?php

namespace App\Reducers;

class TodosReducer
{
    public function __invoke($state, $action)
    {
        global $store;

        switch ($action['type']) {
            case 'GET_TODOS':
                return $action['todos'];
            case 'ADD_TODO':
                $state[] = ['id' => count($state) + 1, 'name' => $action['name'], 'completed' => false];
                return $state;
            case 'TOGGLE_TODO':
                foreach ($state as $key => $todo) {
                    if ($todo['id'] == $action['id']) {
                        $state[$key]['completed'] = $action['completed'];
                    }
                }

                return $state;
            case 'REMOVE_TODO':
                $state = array_filter($state, function ($i) use ($action) {
                    return $i['id'] != $action['id'];
                });
                return $state;
            case 'EDIT_TODO_SAVE':
                foreach ($state as $key => $todo) {
                    if ($todo['id'] == $action['id']) {
                        $state[$key]['name'] = $action['name'];
                    }
                }

                return $state;
            case 'CLEAR_STATE':
                return $store->initialState['todos'];
            default:
                return $state;
        }
    }
}
