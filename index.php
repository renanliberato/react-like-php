<?php

use App\Components\App;
use App\Screens\ActionHistoryScreen;
use App\Screens\TodoListScreen;
use App\Store\ProcessAction;
use App\Store\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');
// define('ROUTE_PREFIX', '/react-like-php'); // used on the website
define('ROUTE_PREFIX', '');

$store = Store::create();

function combineReducers($reducers = [])
{
    return function ($store, $action) use ($reducers) {
        return array_reduce($reducers, function ($state, $reducer) use ($action) {
            return $reducer($state, $action);
        }, $store);
    };
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
        unset($props['children']);
    }

    $attributes = '';
    foreach ($props as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    return "<{$element} {$attributes}>{$children}</{$element}>";
}

function renderComponent($componentName, $props)
{
    return (new $componentName())($props);
}

function getTemplate($file, $params)
{
    extract($params);
    ob_start();
    include $file;
    return ob_get_clean();
}

$slimApp = AppFactory::create();

$slimApp->get(ROUTE_PREFIX.'/', function (Request $request, Response $response, $args) use ($store) {
    $app = renderComponent(TodoListScreen::class, [
        'store' => $store
    ]);

    $template = getTemplate('./layout.html.php', [
        'app' => $app
    ]);

    $response->getBody()->write($template);
    return $response;
});

$slimApp->get(ROUTE_PREFIX.'/history', function (Request $request, Response $response, $args) use ($store) {
    $app = renderComponent(ActionHistoryScreen::class, [
        'store' => $store
    ]);
    $template = getTemplate('./layout.html.php', [
        'app' => $app
    ]);

    $response->getBody()->write($template);
    return $response;
});

$slimApp->post(ROUTE_PREFIX.'/[{path:.*}]', function (Request $request, Response $response, $args) use ($store) {
    (new ProcessAction(combineReducers([
        new \App\Reducers\ClearReducer(Store::INITIAL_STATE),
        new \App\Reducers\AppReducer(),
        new \App\Reducers\HistoryReducer()
    ])))($store, $request->getParsedBody());

    return true;
});

$slimApp->run();
