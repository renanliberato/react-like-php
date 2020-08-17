<?php

namespace App\Components;

use RenanLiberato\Exposer\Components\ActionComponent;
use function RenanLiberato\Exposer\render;
use function RenanLiberato\Exposer\renderComponent;

class NewTodo
{
    public function __invoke($props)
    {
        return renderComponent(ActionComponent::class, [
            'type' => 'ADD_TODO',
            'children' => render('div', [
                'class' => 'form-group',
                'children' => [
                    render('label', [
                        'children' => 'Title'
                    ]),
                    render('input', [
                        'type' => 'text',
                        'name' => 'name',
                        'class' => 'form-control',
                        'children' => render('button', [
                            'style' => 'margin-top: 10px !important;',
                            'class' => 'btn btn-primary active',
                            'children' => 'Add'
                        ])
                    ])
                ]
            ])
        ]);
    }
}
