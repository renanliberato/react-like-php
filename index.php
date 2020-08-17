<?php

use App\Screens\ActionHistoryScreen;
use App\Screens\TodoListScreen;
use App\Store\ProcessAction;
use App\Store\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use function RenanLiberato\Exposer\renderComponent;

require './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');
// define('ROUTE_PREFIX', '/react-like-php'); // used on the website
define('ROUTE_PREFIX', '');

if (!file_exists('./data/todos.json')) {
    file_put_contents('./data/todos.json', "[]");
}

if (!file_exists('./data/actions_history.json')) {
    file_put_contents('./data/actions_history.json', "[]");
}

$initialState = [
    'todos' => [],
    'ui' => [
        'editing_todo' => false
    ],
    'actions_history' => [],
    'user_id' => null
];

$store = new Store(
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

$store->setPersistFunction(function ($state) {
    file_put_contents('./data/todos.json', json_encode($state['todos'], JSON_PRETTY_PRINT));
    file_put_contents('./data/actions_history.json', json_encode($state['actions_history'], JSON_PRETTY_PRINT));

    return [
        'ui' => $state['ui'],
        'user_id' => $state['user_id']
    ];
});

$store->getPersistedState();
$store->action([
    'type' => 'GET_TODOS',
    'todos' => json_decode(file_get_contents('./data/todos.json'), true),
]);
$store->action([
    'type' => 'GET_HISTORY',
    'actions_history' => json_decode(file_get_contents('./data/actions_history.json'), true),
]);

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
    (new ProcessAction($store))($request->getParsedBody());

    return true;
});

$slimApp->run();
