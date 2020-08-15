<?php

namespace App\Components;

class App
{
    public function __invoke($props)
    {
        $history = $props['store']['actions_history'];

        $storeLimitReached = strlen(urlencode(json_encode($props['store']))) > 4000 ? 'true' : 'false';

        return render('div', [
            'children' => [
                $storeLimitReached != 'true' ? '' : render('div', [
                    'attributes' => [
                        'class' => 'alert alert-warning'
                    ],
                    'children' => "Cookie limit is (maybe almost) reached, some actions might not work. Press 'Clear State' button to keep using the app."
                ]),
                render('div', [
                    'attributes' => [
                        'style' => 'margin-bottom: 10px;'
                    ],
                    'children' => renderComponent(ActionComponent::class, [
                        'type' => 'CLEAR_STATE',
                        'children' => render('button', [
                            'attributes' => [
                                'class' => 'btn btn-primary active',
                            ],
                            'children' => "Clear state"
                        ])
                    ]),
                ]),
                render('div', [
                    'attributes' => [
                        'style' => 'flex-direction: row; justify-content: space-between;'
                    ],
                    'children' => [
                        render('div', [
                            'attributes' => [
                                'class' => 'card',
                                'style' => 'align-self: flex-start; width: 45%; padding: 20px;'
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
                        render('div', [
                            'attributes' => [
                                'class' => 'card',
                                'style' => 'align-self: flex-start; width: 45%; padding: 20px;'
                            ],
                            'children' => [
                                render('h1', ['children' => 'Actions history']),
                                renderComponent(ActionsHistory::class, ['history' => $history])
                            ]
                        ]),
                    ]
                ])
            ]
        ]);
    }
}
