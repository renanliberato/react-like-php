<?php

namespace App\Components;

use RenanLiberato\Exposer\Components\ActionComponent;
use function RenanLiberato\Exposer\render;
use function RenanLiberato\Exposer\renderComponent;

class EditTodo
{
    public function __invoke($props)
    {
        return renderComponent(ActionComponent::class, [
            'type' => 'EDIT_TODO_SAVE',
            'params' => [
                'id' => $props['todo']['id'],
                'formAttributes' => [
                    'class' => 'form-inline',
                    'style' => 'width: 100%;'
                ]
            ],
            'children' => render('div', [
                'class' => 'form-group',
                'style' => 'width: 100%; flex-direction: row; justify-content: space-between; align-items: flex-end;',
                'children' => [
                    render('div', [
                        'style' => 'flex: 1; align-items: flex-start;',
                        'children' => [
                            render('label', ['children' => 'Name']),
                            render('input', [
                                'type' => 'text',
                                'name' => 'name',
                                'value' => $props['todo']['name'],
                                'class' => 'form-control',
                                'style' => 'align-self: stretch;',
                            ]),
                        ]
                    ]),
                    render('button', [
                        'class' => 'btn btn-primary active',
                        'children' => 'Save'
                    ])
                ]
            ])
        ]);
    }
}
