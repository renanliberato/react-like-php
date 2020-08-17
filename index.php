<?php

use App\Store\AppStore;
use App\Screens\ActionHistoryScreen;
use App\Screens\TodoListScreen;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RenanLiberato\ExposerStore\Persistors\CookiePersistor;
use Slim\Factory\AppFactory;

use RenanLiberato\ExposerStore\Store\ProcessAction;
use function RenanLiberato\Exposer\renderComponent;

require './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');
// define('ROUTE_PREFIX', '/react-like-php'); // used on the website
define('ROUTE_PREFIX', '');

$initialState = [
    'todos' => [],
    'ui' => [
        'editing_todo' => false
    ],
    'actions_history' => [],
    'user_id' => null
];

$store = new AppStore(
    $initialState,
    [
        new \App\Reducers\AppReducer(),
    ],
    [
        \App\Middlewares\LoginMiddleware::class,
        \App\Middlewares\ClearMiddleware::class,
        \App\Middlewares\HistoryMiddleware::class,
    ]
);

$store->setPersistor(new CookiePersistor(TOKEN_COOKIE_NAME, '!#OIGJ!#$F12ofij123fo123FJ!@3'));

$store->getPersistedState();

function getTemplate($file, $params)
{
    extract($params);
    ob_start();
    include $file;
    return ob_get_clean();
}

$slimApp = AppFactory::create();

$slimApp->add(function ($request, $handler) use ($store) {
    $response = $handler->handle($request);

    if ($request->getMethod() != 'POST') {
        $store->persistState();
    }

    return $response;
});

$slimApp->get(ROUTE_PREFIX . '/', function (Request $request, Response $response, $args) use ($store) {
    $app = renderComponent(TodoListScreen::class, [
        'store' => $store->getState()
    ]);

    $template = getTemplate('./layout.html.php', [
        'app' => $app
    ]);

    $response->getBody()->write($template);
    return $response;
});

$slimApp->get(ROUTE_PREFIX . '/history', function (Request $request, Response $response, $args) use ($store) {
    $app = renderComponent(ActionHistoryScreen::class, [
        'store' => $store->getState()
    ]);
    $template = getTemplate('./layout.html.php', [
        'app' => $app
    ]);

    $response->getBody()->write($template);
    return $response;
});

$slimApp->post(ROUTE_PREFIX . '/[{path:.*}]', function (Request $request, Response $response, $args) use ($store) {
    (new ProcessAction($store, function () {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("Location: " . $actual_link);

        exit();
    }))($request->getParsedBody());

    return true;
});

$slimApp->run();
