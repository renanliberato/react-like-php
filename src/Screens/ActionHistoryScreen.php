<?php

namespace App\Screens;

use App\Components\ActionsHistory;
use RenanLiberato\Exposer\Components\ActionComponent;
use function RenanLiberato\Exposer\render;
use function RenanLiberato\Exposer\renderComponent;

class ActionHistoryScreen
{
    public function __invoke($props)
    {
        $history = $props['store']['actions_history'];

        return render('div', [
            'style' => 'margin: 10px;',
            'children' => [
                render('div', [
                    'style' => 'flex-direction: row; justifty-content: space-between; margin-bottom: 10px',
                    'children' => [
                        render('a', [
                            'class' => 'btn btn-outline-primary',
                            'href' => ROUTE_PREFIX.'/',
                            'style' => 'align-self: flex-start;',
                            'children' => "Back"
                        ]),
                        render('div', [
                            'style' => 'flex: 1',
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
                        render('h1', ['children' => 'Actions history']),
                        renderComponent(ActionsHistory::class, ['history' => $history])
                    ]
                ]),
            ]
        ]);
    }
}
