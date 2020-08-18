<?php

namespace App\Reducers;

class HistoryReducer
{
    public function __invoke($state, $action)
    {
        $newState = $state;
        
        switch ($action['type']) {
            case 'CLEAR_STATE':
                $newState = [];
            default:
        }

        if ($action['type'] != 'INITIALIZE') {
            $newState[] = ['timestamp' => date('H:i:s'), 'action' => $action];
        }

        return $newState;
    }
}
