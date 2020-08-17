<?php

namespace App\Middlewares;

use App\Store\AppStore;

class ClearMiddleware
{
    private $store;

    public function __construct(AppStore $store)
    {
        $this->store = $store;
    }

    public function __invoke($next)
    {
        return function ($action) use ($next) {
            switch ($action['type']) {
                case 'CLEAR_STATE':
                    $state = $this->store->getState();
                    $state['todos'] = [];
                    $state['actions_history'] = [];

                    $this->store->setState($state);
            }

            return $next($action);
        };
    }
}
