<?php

namespace App\Store;

class Store
{
    public static $JWT_KEY = '!#OIGJ!#$F12ofij123fo123FJ!@3';

    public $initialState;

    private $mainReducer;

    /**
     * @var array
     */
    private $state;

    private $middlewares;

    private $persistFunction;

    public function __construct($state, $reducers, $middlewares)
    {
        $this->initialState = $state;
        $this->state = $state;
        $this->mainReducer = $this->combineReducers($reducers);
        $this->middlewares = $middlewares;
        $this->middlewares = $this->applyMiddlewares();
        $this->persistFunction = function ($state) {
            return $state;
        };

        $this->action(['type' => 'INITIALIZE']);
    }

    private function combineReducers($reducers = [])
    {
        return function ($action) use ($reducers) {
            return array_reduce($reducers, function ($state, $reducer) use ($action) {
                $newState = $reducer($state, $action);
                return $newState;
            }, $this->getState());
        };
    }

    /**
     * @return array
     */
    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function action($action)
    {
        ($this->middlewares[0])($action);
        $this->state = ($this->mainReducer)($action);
    }

    public function getPersistedState()
    {
        if (isset($_COOKIE[TOKEN_COOKIE_NAME])) {
            $this->setState(array_merge($this->state, json_decode(json_encode((array)\Firebase\JWT\JWT::decode($_COOKIE[TOKEN_COOKIE_NAME], self::$JWT_KEY, ['HS256'])), true)));
        }
    }

    public function setPersistFunction($persistFunction)
    {
        $this->persistFunction = $persistFunction;
    }

    public function persistState()
    {
        $jwt = \Firebase\JWT\JWT::encode(($this->persistFunction)($this->getState()), Store::$JWT_KEY);

        setcookie(TOKEN_COOKIE_NAME, $jwt);
    }

    public function applyMiddlewares()
    {
        $defaultNext = function ($action) {
            return $action;
        };

        $i = 0;
        if ($this->middlewares == null || count($this->middlewares) == 0) {
            return [$defaultNext];
        }

        if (count($this->middlewares) == 1) {
            return [
                (new $this->middlewares[0]($this))($defaultNext)
            ];
        }

        $middlewaresLength = count($this->middlewares);
        
        $this->middlewares = array_map(function ($middleware) {
            return (new $middleware($this));
        }, $this->middlewares);
        
        $i = count($this->middlewares) - 1;
        
        $middlewaresWithNext = [];
        while ($i >= 0) {
            if ($i == count($this->middlewares) - 1) {
                $middlewaresWithNext[$i] = $this->middlewares[$i]($defaultNext);
            } else {
                $middlewaresWithNext[$i] = $this->middlewares[$i]($middlewaresWithNext[$i + 1]);
            }

            $i--;
        }

        return $middlewaresWithNext;
    }
}
