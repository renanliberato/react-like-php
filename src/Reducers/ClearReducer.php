<?php

namespace App\Reducers;

class ClearReducer
{
    private $initialState;

    public function __construct($initialState)
    {
        $this->initialState = $initialState;
    }

    public function __invoke($state, $action)
    {
        switch ($action['type']) {
            case 'CLEAR_STATE':
                return $this->initialState;
            default:
                return $state;
        }
    }
}
