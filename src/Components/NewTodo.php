<?php

namespace App\Components;

class NewTodo
{
    public function __invoke($props)
    {
        return renderComponent(ActionComponent::class, [
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
}
