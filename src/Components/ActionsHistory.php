<?php

namespace App\Components;

class ActionsHistory
{
    public function __invoke($props)
    {
        $history = $props['history'];
        usort($history, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return render('div', [
            'children' => render('ul', [
                'attributes' => [
                    'class' => 'list-group',
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
                                    render('div', [
                                        'children' => [
                                            render('span', ['children' => $action['timestamp']]),
                                            render('span', [
                                                'attributes' => [
                                                    'style' => 'line-height: 1.5em'
                                                ],
                                                'children' => json_encode($action['action'])
                                            ]),
                                        ]
                                    ])
                                ]
                            ])
                        ]
                    ]);
                }, $history)
            ])
        ]);
    }
}
