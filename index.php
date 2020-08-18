<?php

use App\Reducers\HistoryReducer;
use App\Reducers\TodosReducer;
use App\Reducers\UIReducer;
use App\Reducers\UserReducer;
use App\Store\AppStore;
use App\Screens\ActionHistoryScreen;
use App\Screens\TodoListScreen;
use App\Store\FileSystemPersistor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RenanLiberato\ExposerStore\Persistors\CookiePersistor;
use Slim\Factory\AppFactory;

use RenanLiberato\ExposerStore\Store\ProcessAction;
use function RenanLiberato\Exposer\renderComponent;

require './vendor/autoload.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');
// define('ROUTE_PREFIX', '/react-like-php'); // used on the website
define('ROUTE_PREFIX', '');

$initialState = [
    'todos' => [
        ['id' => 4, 'name' => "Give a star to <a target=_blank href=https://github.com/renanliberato/exposer>renanliberato/exposer</a>", 'completed' => false],
        ['id' => 3, 'name' => "Give a star to <a target=_blank href=https://github.com/renanliberato/exposer-store>renanliberato/exposer-store</a>", 'completed' => false],
        ['id' => 2, 'name' => "<a target=_blank href=https://twitter.com/renanlibegato>Follow me on twitter</a>", 'completed' => false],
        ['id' => 1, 'name' => "Enjoy the day!", 'completed' => false],
    ],
    'ui' => [
        'editing_todo' => false
    ],
    'actions_history' => [],
    'user_id' => null
];

$store = new AppStore(
    $initialState,
    [
        'ui' => new UIReducer(),
        'todos' => new TodosReducer(),
        'actions_history' => new HistoryReducer(),
        'user_id' => new UserReducer()
    ]
);

$store->setPersistor(new FileSystemPersistor(new CookiePersistor(TOKEN_COOKIE_NAME, '!#OIGJ!#$F12ofij123fo123FJ!@3')));

$store->getPersistedState();

$state = $store->getState();

if (!$state['user_id']) {
    $store->action([
        'type' => 'LOGIN',
        'user_id' => \Ramsey\Uuid\Uuid::uuid4()->toString()
    ]);
}

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
        // return true;
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("Location: " . $actual_link);

        exit();
    }))($request->getParsedBody());

    return true;
});

$slimApp->run();
