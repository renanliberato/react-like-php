<?php

require './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TOKEN_COOKIE_NAME', 'APP_STATE_TOKEN');

$jwtkey = '!#OIGJ!#$F12ofij123fo123FJ!@3';

$initialState = [
    'todos' => [],
    'ui' => [
        'editing_todo' => false
    ],
    'actions_history' => []
];

$store = $initialState;

if (!isset($_COOKIE[TOKEN_COOKIE_NAME])) {
    $jwt = \Firebase\JWT\JWT::encode($store, $jwtkey);

    setcookie(TOKEN_COOKIE_NAME, $jwt);
} else {
    $store = json_decode(json_encode((array)\Firebase\JWT\JWT::decode($_COOKIE[TOKEN_COOKIE_NAME], $jwtkey, ['HS256'])), true);
}

function appReducer($state, $action)
{
    switch ($action['type']) {
        case 'ADD_TODO':
            $state['todos'][] = ['id' => count($state['todos']) + 1, 'name' => $action['name']];
            return $state;
        case 'EDIT_TODO_SHOW':
            $state['ui']['editing_todo'] = $action['id'];
            return $state;
        case 'REMOVE_TODO':
            $state['todos'] = array_filter($state['todos'], function ($i) use ($action) {
                return $i['id'] != $action['id'];
            });
            return $state;
        case 'EDIT_TODO_SAVE':
            foreach ($state['todos'] as $key => $todo) {
                if ($todo['id'] == $action['id']) {
                    $state['todos'][$key]['name'] = $action['name'];
                }
            }
            $state['ui']['editing_todo'] = false;

            return $state;
        default:
            return $state;
    }
}

function historyReducer($state, $action)
{
    $state['actions_history'][] = ['timestamp' => date('H:i:s'), 'action' => $action];

    return $state;
}

function mainReducer($reducers = [])
{
    return function ($store, $action) use ($reducers) {
        return array_reduce($reducers, function ($state, $reducer) use ($action) {
            return $reducer($state, $action);
        }, $store);
    };
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bodyContent = $_POST;

    $store = mainReducer([appReducer::class, historyReducer::class])($store, $bodyContent);

    $jwt = \Firebase\JWT\JWT::encode($store, $jwtkey);

    setcookie(TOKEN_COOKIE_NAME, $jwt);

    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    header("Location: " . $actual_link);
    exit();
}

function ActionComponent($props = [])
{
    $formAttributes = [];

    $type = $props['type'];
    $params = isset($props['params']) ? $props['params'] : [];
    $children = $props['children'];

    if (isset($params['formAttributes'])) {
        $formAttributes = $params['formAttributes'];
        unset($params['formAttributes']);
    }

    return render('form', [
        'attributes' => array_merge(
            ['method' => 'post', 'action' => ''],
            $formAttributes
        ),
        'children' => array_merge(
            [render('input', [
                'attributes' => [
                    'type' => 'hidden',
                    'name' => 'type',
                    'value' => $type
                ]
            ])],
            array_map(function ($key) use ($params) {
                return render('input', [
                    'attributes' => [
                        'type' => 'hidden',
                        'name' => $key,
                        'value' => $params[$key]
                    ]
                ]);
            }, array_keys($params)),
            is_array($children) ? $children : [$children]
        )
    ]);
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

function row($children)
{
    return render('div', [
        'attributes' => [
            'class' => 'row',
        ],
        'children' => $children
    ]);
}

function TodoList($props = [])
{
    return row([
        'children' => render('ul', [
            'attributes' => [
                'class' => 'list-group'
            ],
            'children' => array_map(function ($todo) use ($props) {
                if ($props['editing_todo'] == $todo['id'])
                    return render('li', [
                        'attributes' => [
                            'class' => 'list-group-item',
                        ],
                        'children' => EditTodo(['todo' => $todo])
                    ]);
                else
                    return render('li', [
                        'attributes' => [
                            'class' => 'list-group-item',
                            'style' => 'align-items: center; justify-content: space-between;'
                        ],
                        'children' => [
                            render('span', ['children' => $todo['name']]),
                            render('div', [
                                'attributes' => [
                                    'style' => 'flex-direction: row;'
                                ],
                                'children' => [
                                    ActionComponent([
                                        'type' => 'EDIT_TODO_SHOW',
                                        'params' => [
                                            'id' => $todo['id'],
                                            'formAttributes' => [
                                                'style' => 'margin-right: 10px;'
                                            ]
                                        ],
                                        'children' => render('button', ['attributes' => ['class' => 'btn btn-outline-primary'], 'children' => 'Edit'])
                                    ]),
                                    ActionComponent([
                                        'type' => 'REMOVE_TODO',
                                        'params' => ['id' => $todo['id']],
                                        'children' => render('button', ['attributes' => ['class' => 'btn btn-outline-primary'], 'children' => 'Remove'])
                                    ]),
                                ]
                            ])
                        ]
                    ]);
            }, $props['todos'])
        ])
    ]);
}

function ActionsHistory($props = [])
{
    $history = $props['history'];
    usort($history, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    return row([
        'children' => render('ul', [
            'attributes' => [
                'class' => 'list-group'
            ],
            'children' => array_map(function ($action) use ($props) {
                return render('li', [
                    'attributes' => [
                        'class' => 'list-group-item',
                        'style' => 'align-items: center; justify-content: space-between;'
                    ],
                    'children' => [
                        render('details', [
                            'children' => [
                                render('summary', [
                                    'attributes' => [
                                        'style' => 'margin-bottom: 10px'
                                    ],
                                    'children' => render('strong', [
                                        'children' => "{$action['action']['type']}"
                                    ])
                                ]),
                                render('span', ['children' => $action['timestamp']]),
                                render('span', ['children' => json_encode($action['action'])]),
                            ]
                        ])
                    ]
                ]);
            }, $history)
        ])
    ]);
}

function NewTodo($props = [])
{
    return ActionComponent([
        'type' => 'ADD_TODO',
        'children' => render('div', [
            'attributes' => [
                'class' => 'form-group'
            ],
            'children' => [
                render('label', [
                    'children' => 'Title'
                ]),
                render('input', [
                    'attributes' => [
                        'type' => 'text',
                        'name' => 'name',
                        'class' => 'form-control'
                    ],
                    'children' => render('button', [
                        'attributes' => [
                            'style' => 'margin-top: 10px !important;',
                            'class' => 'btn btn-primary active'
                        ],
                        'children' => 'Add'
                    ])
                ])
            ]
        ])
    ]);
}

function EditTodo($props = [])
{
    return ActionComponent([
        'type' => 'EDIT_TODO_SAVE',
        'params' => [
            'id' => $props['todo']['id'],
            'formAttributes' => [
                'class' => 'form-inline'
            ]
        ],
        'children' => render('div', [
            'attributes' => [
                'class' => 'form-group',
            ],
            'children' => [
                render('label', ['children' => 'Name']),
                render('input', [
                    'attributes' => [
                        'type' => 'text',
                        'name' => 'name',
                        'value' => $props['todo']['name'],
                        'class' => 'form-control'
                    ]
                ]),
                render('button', [
                    'attributes' => [
                        'class' => 'btn btn-primary active',
                    ],
                    'children' => 'Save'
                ])
            ]
        ])
    ]);
}

function App($props = [])
{
    $history = $props['store']['actions_history'];

    return render('div', [
        'attributes' => [
            'style' => 'flex-direction: row; justify-content: space-evenly;'
        ],
        'children' => [
            render('div', [
                'attributes' => [
                    'class' => 'card',
                    'style' => 'align-self: flex-start; width: 30%; padding: 20px;'
                ],
                'children' => [
                    render('h1', ['children' => 'Todo List']),
                    NewTodo(),
                    TodoList(['todos' => $props['store']['todos'], 'editing_todo' => $props['store']['ui']['editing_todo']])
                ]
            ]),
            render('div', [
                'attributes' => [
                    'class' => 'card',
                    'style' => 'align-self: flex-start; width: 30%; padding: 20px;'
                ],
                'children' => [
                    render('h1', ['children' => 'Actions history']),
                    ActionsHistory(['history' => $history])
                ]
            ]),
        ]
    ]);
}

$app = App([
    'store' => $store
]);

require './layout.html.php';
