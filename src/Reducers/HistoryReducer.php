<?php

namespace App\Reducers;

class HistoryReducer
{
    public function __invoke($state, $action)
    {
        $state['actions_history'][] = ['timestamp' => date('H:i:s'), 'action' => $action];

        return $state;
    }
}
