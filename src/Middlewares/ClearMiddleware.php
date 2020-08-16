<?php

namespace App\Middlewares;

use App\Store\Store;

class ClearMiddleware
{
    private $initialState = Store::INITIAL_STATE;
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __invoke($next)
    {
        return function ($action) use ($next) {
            switch ($action['type']) {
                case 'CLEAR_STATE':
                    $state = $this->store->getState();
                    foreach ($state as $key => $value) {
                        $state[$key] = $this->initialState[$key];
                    }

                    $this->store->setState($state);
            }

            return $next($action);
        };
    }
}
