<?php

namespace App\Components;

class TodoList
{
    public function __invoke($props)
    {
        return render('div', [
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
                            'children' => (new EditTodo())(['todo' => $todo])
                        ]);
                    else
                        return render('li', [
                            'attributes' => [
                                'class' => 'list-group-item',
                                'style' => 'align-items: center; justify-content: space-between;'
                            ],
                            'children' => [
                                renderComponent(ActionComponent::class, [
                                    'type' => 'TOGGLE_TODO',
                                    'params' => [
                                        'id' => $todo['id'],
                                        'completed' => !$todo['completed'],
                                        'formAttributes' => [
                                            'id' => 'formtoggle' . $todo['id'],
                                        ],
                                    ],
                                    'children' => render('div', [
                                        'attributes' => [
                                            'class' => 'checkbox',
                                            'style' => 'margin-top: -10px; margin-right: -15px;'
                                        ],
                                        'children' => render('label', [
                                            'attributes' => [
                                                'class' => 'checkbox-inline'
                                            ],
                                            'children' => render('input', [
                                                'attributes' => array_merge(
                                                    [
                                                        'type' => 'checkbox',
                                                        'class' => 'react-like-submittable',
                                                        'data-form-id' => 'formtoggle' . $todo['id']
                                                    ],
                                                    $todo['completed'] ? ['checked' => true] : []
                                                ),
                                            ])
                                        ])
                                    ]),
                                ]),
                                render('span', ['attributes' => ['style' => 'flex: 1;'], 'children' => $todo['name']]),
                                render('div', [
                                    'attributes' => [
                                        'style' => 'flex-direction: row;'
                                    ],
                                    'children' => [
                                        renderComponent(ActionComponent::class, [
                                            'type' => 'EDIT_TODO_SHOW',
                                            'params' => [
                                                'id' => $todo['id'],
                                                'formAttributes' => [
                                                    'style' => 'margin-right: 10px;'
                                                ]
                                            ],
                                            'children' => render('button', ['attributes' => ['class' => 'btn btn-outline-primary', 'style' => 'margin-right: 10px;'], 'children' => 'Edit'])
                                        ]),
                                        renderComponent(ActionComponent::class, [
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
}
