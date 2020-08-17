<?php

namespace App\Middlewares;

use App\Store\AppStore;

class HistoryMiddleware
{
    private $store;

    public function __construct(AppStore $store)
    {
        $this->store = $store;
    }

    public function __invoke($next)
    {
        return function ($action) use ($next) {
            if ($action['type'] != 'INITIALIZE') {
                $state = $this->store->getState();
                $state['actions_history'][] = ['timestamp' => date('H:i:s'), 'action' => $action];
                $this->store->setState($state);
            }

            return ($next)($action);
        };
    }
}
