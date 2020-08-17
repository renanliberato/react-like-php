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
        $storeLimitReached = strlen(urlencode(json_encode($props['store']))) > 4000 ? 'true' : 'false';

        return render('div', [
            'style' => [
                'align-self' => 'center',
                'width' => '45%'
            ],
            'children' => [
                $storeLimitReached != 'true' ? '' : render('div', [
                    'class' => 'alert alert-warning',
                    'children' => "Cookie limit is (maybe almost) reached, some actions might not work. Press 'Clear State' button to keep using the app."
                ]),
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
