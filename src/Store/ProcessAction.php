<?php

namespace App\Store;

class ProcessAction
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __invoke($action)
    {
        $this->store->action($action);
    
        $this->store->persistState();
    
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("Location: " . $actual_link);
        
        exit();
    }
}