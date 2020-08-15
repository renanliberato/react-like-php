<?php

namespace App\Store;

class ProcessAction
{
    private $mainReducer;

    public function __construct($mainReducer)
    {
        $this->mainReducer = $mainReducer;
    }

    public function __invoke($store, $action)
    {
        $store = ($this->mainReducer)($store, $action);
    
        $jwt = \Firebase\JWT\JWT::encode($store, Store::JWT_KEY);
    
        setcookie(TOKEN_COOKIE_NAME, $jwt);
    
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("Location: " . $actual_link);
        
        exit();
    }
}