<?php

namespace App\Middlewares;

use App\Store\AppStore;

class LoginMiddleware
{
    private $store;

    public function __construct(AppStore $store)
    {
        $this->store = $store;
    }

    public function __invoke($next)
    {
        return function ($action) use ($next) {
            $state = $this->store->getState();
            if (!$state['user_id']) {
                $state['user_id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $this->store->setState($state);
            }
    
            return ($next)($action);
        };
    }
}
