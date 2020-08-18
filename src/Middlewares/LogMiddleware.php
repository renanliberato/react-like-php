<?php

namespace App\Middlewares;

use RenanLiberato\ExposerStore\Middlewares\AbstractMiddleware;

class LogMiddleware extends AbstractMiddleware
{
    public function process($action)
    {
        var_dump($action);

        return $this->next->process($action);
    }
}