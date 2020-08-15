<?php

namespace App\Screens;

use App\Components\ActionComponent;
use App\Components\NewTodo;
use App\Components\TodoList;

class TodoListScreen
{
    public function __invoke($props)
    {
        $storeLimitReached = strlen(urlencode(json_encode($props['store']))) > 4000 ? 'true' : 'false';

        return render('div', [
            'attributes' => [
                'style' => 'align-self: center; width: 45%;'
            ],
            'children' => [
                $storeLimitReached != 'true' ? '' : render('div', [
                    'attributes' => [
                        'class' => 'alert alert-warning'
                    ],
                    'children' => "Cookie limit is (maybe almost) reached, some actions might not work. Press 'Clear State' button to keep using the app."
                ]),
                render('div', [
                    'attributes' => [
                        'style' => 'flex-direction: row; justifty-content: space-between; margin-bottom: 10px'
                    ],
                    'children' => [
                        render('a', [
                            'attributes' => [
                                'class' => 'btn btn-outline-primary',
                                'href' => '/history'
                            ],
                            'children' => "See history"
                        ]),
                        render('div', [
                            'attributes' => [
                                'style' => 'flex: 1'
                            ]
                        ]),
                        renderComponent(ActionComponent::class, [
                            'type' => 'CLEAR_STATE',
                            'children' => render('button', [
                                'attributes' => [
                                    'class' => 'btn btn-primary active',
                                ],
                                'children' => "Clear state"
                            ])
                        ]),
                    ]
                ]),
                render('div', [
                    'attributes' => [
                        'class' => 'card',
                        'style' => 'padding: 20px;'
                    ],
                    'children' => [
                        render('h1', ['children' => 'Todo List']),
                        render('p', [
                            'children' => 'This app uses some ReactJS and Redux concepts to implement its UI and state management. <br/>It shows us that functional components, actions, and reducers are not exclusive to a libraries, frameworks, or languages. '
                        ]),
                        renderComponent(NewTodo::class, []),
                        renderComponent(TodoList::class, ['todos' => $props['store']['todos'], 'editing_todo' => $props['store']['ui']['editing_todo']])
                    ]
                ]),
            ]
        ]);
    }
}
