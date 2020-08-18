<?php

namespace App\Components;

use RenanLiberato\Exposer\Components\ActionComponent;
use function RenanLiberato\Exposer\render;
use function RenanLiberato\Exposer\renderComponent;

class TodoList
{
    public function __invoke($props)
    {
        $todos = $props['todos'];

        usort($todos, function($a, $b) {
            return $b['id'] - $a['id'];
        });

        return render('div', [
            'children' => render('ul', [
                'class' => 'list-group',
                'children' => array_map(function ($todo) use ($props) {
                    if ($props['editing_todo'] == $todo['id'])
                        return render('li', [
                            'class' => 'list-group-item',
                            'children' => (new EditTodo())(['todo' => $todo])
                        ]);
                    else
                        return render('li', [
                            'class' => 'list-group-item',
                            'style' => 'align-items: center; justify-content: space-between;',
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
                                        'class' => 'checkbox',
                                        'style' => 'margin-top: -10px; margin-right: -15px;',
                                        'children' => render('label', [
                                            'class' => 'checkbox-inline',
                                            'children' => render('input', array_merge(
                                                [
                                                    'type' => 'checkbox',
                                                    'class' => 'react-like-submittable',
                                                    'data-form-id' => 'formtoggle' . $todo['id'],
                                                ],
                                                $todo['completed'] ? ['checked' => true] : []
                                            ))
                                        ])
                                    ]),
                                ]),
                                render('span', ['style' => 'flex: 1;', 'children' => $todo['name']]),
                                render('div', [
                                    'class' => 'btn-group',
                                    'children' => [
                                        render('button', [
                                            'class' => 'btn bmd-btn-icon dropdown-toggle',
                                            'style' => [
                                                'margin-right' => '0!important'
                                            ],
                                            'type' => 'button',
                                            'id' => "btn-options-{$todo['id']}",
                                            'data-toggle' => 'dropdown',
                                            'aria-haspopup' => 'true',
                                            'aria-expanded' => 'false',
                                            'children' => render('i', [
                                                'class' => 'material-icons',
                                                'children' => 'more_vert',
                                                'style' => [
                                                    'margin-right' => '0!important'
                                                ],
                                            ]),
                                        ]),
                                        render('div', [
                                            'class' => 'dropdown-menu dropdown-menu-left',
                                            'aria-labelledby' => "btn-options-{$todo['id']}",
                                            'children' => [
                                                renderComponent(ActionComponent::class, [
                                                    'type' => 'EDIT_TODO_SHOW',
                                                    'params' => [
                                                        'id' => $todo['id'],
                                                        'formAttributes' => [
                                                            'style' => 'margin-right: 10px;'
                                                        ]
                                                    ],
                                                    'children' => render('button', ['class' => 'btn dropdown-item', 'type' => 'button', 'children' => 'Edit'])
                                                ]),
                                                renderComponent(ActionComponent::class, [
                                                    'type' => 'REMOVE_TODO',
                                                    'params' => ['id' => $todo['id']],
                                                    'children' => render('button', ['class' => 'btn dropdown-item', 'type' => 'button', 'children' => 'Remove'])
                                                ]),
                                            ]
                                        ])
                                    ]
                                ])
                            ]
                        ]);
                }, $todos)
            ])
        ]);
    }
}
