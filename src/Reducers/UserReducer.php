<?php

namespace App\Reducers;

class UserReducer
{
    public function __invoke($state, $action)
    {
        switch ($action['type']) {
            case 'LOGIN':
                return $action['user_id'];
            default:
                return $state;
        }
    }
}
