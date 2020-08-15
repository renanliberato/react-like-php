<?php

use App\Components\App;
use App\Store\ProcessAction;
use App\Store\Store;

require './vendor/autoload.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');

$store = Store::create();

function combineReducers($reducers = [])
{
    return function ($store, $action) use ($reducers) {
        return array_reduce($reducers, function ($state, $reducer) use ($action) {
            return $reducer($state, $action);
        }, $store);
    };
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bodyContent = $_POST;
    
    (new ProcessAction(combineReducers([
        new \App\Reducers\ClearReducer(Store::INITIAL_STATE),
        new \App\Reducers\AppReducer(),
        new \App\Reducers\HistoryReducer()
    ])))($store, $bodyContent);
}

function render($element = "span", $props = [])
{
    $children = '';
    if (isset($props['children'])) {
        if (is_array($props['children'])) {
            $children = join('', $props['children']);
        } else {
            $children = $props['children'];
        }
    }

    $attributes = '';
    if (isset($props['attributes'])) {
        foreach ($props['attributes'] as $key => $value) {
            $attributes .= " {$key}=\"{$value}\"";
        }
    }

    return "<{$element} {$attributes}>{$children}</{$element}>";
}

function renderComponent($componentName, $props) {
    return (new $componentName())($props);
}

$app = renderComponent(App::class, [
    'store' => $store
]);

require './layout.html.php';
