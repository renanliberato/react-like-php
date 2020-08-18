<?php

namespace App\Screens;

use App\Components\NewTodo;
use App\Components\TodoList;
use RenanLiberato\Exposer\Components\ActionComponent;
use function RenanLiberato\Exposer\render;
use function RenanLiberato\Exposer\renderComponent;

class TodoListScreen
{
    public function __invoke($props)
    {
        return render('div', [
            'style' => [
                'margin' => '10px',
            ],
            'children' => [
                render('div', [
                    'style' => 'flex-direction: row; justifty-content: space-between; margin-bottom: 10px',
                    'children' => [
                        render('a', [
                            'class' => 'btn btn-outline-primary',
                            'href' => ROUTE_PREFIX.'/history',
                            'children' => "See history"
                        ]),
                        render('div', [
                            'style' => [
                                'flex' => 1
                            ],
                        ]),
                        renderComponent(ActionComponent::class, [
                            'type' => 'CLEAR_STATE',
                            'children' => render('button', [
                                'class' => 'btn btn-primary active',
                                'children' => "Clear state"
                            ])
                        ]),
                    ]
                ]),
                render('div', [
                    'class' => 'card',
                    'style' => 'padding: 20px;',
                    'children' => [
                        render('h1', ['children' => 'Todo List']),
                        render('p', [
                            'children' => 'This app uses some ReactJS and Redux concepts to implement its UI and state management'
                        ]),
                        render('p', [
                            'children' => 'It shows us that functional components, actions, and reducers are not exclusive to a libraries, frameworks, or languages.'
                        ]),
                        render('p', [
                            'children' => 'Checkout the main libraries that were created from this idea:'
                        ]),
                        render('ul', [
                            'children' => [
                                render('li', [
                                    'children' => '<a target=_blank href=https://github.com/renanliberato/exposer>renanliberato/exposer</a>'
                                ]),
                                render('li', [
                                    'children' => '<a target=_blank href=https://github.com/renanliberato/exposer-store>renanliberato/exposer-store</a>'
                                ]),
                            ]
                        ]),
                        renderComponent(NewTodo::class, []),
                        renderComponent(TodoList::class, ['todos' => $props['store']['todos'], 'editing_todo' => $props['store']['ui']['editing_todo']])
                    ]
                ]),
            ]
        ]);
    }
}
