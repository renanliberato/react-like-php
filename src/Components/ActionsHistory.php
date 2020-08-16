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
                'class' => 'list-group',
                'children' => array_map(function ($action) use ($props) {
                    return render('li', [
                        'class' => 'list-group-item',
                        'style' => 'align-items: center; justify-content: space-between;',
                        'children' => [
                            render('details', [
                                'children' => [
                                    render('summary', [
                                        'style' => 'margin-bottom: 10px',
                                        'children' => render('strong', [
                                            'children' => "{$action['action']['type']}"
                                        ])
                                    ]),
                                    render('div', [
                                        'children' => [
                                            render('span', ['children' => $action['timestamp']]),
                                            render('span', [
                                                'style' => 'line-height: 1.5em',
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
