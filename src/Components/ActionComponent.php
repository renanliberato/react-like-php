<?php

namespace App\Components;

class ActionComponent
{
    public function __invoke($props)
    {
        $formAttributes = [];

        $type = $props['type'];
        $params = isset($props['params']) ? $props['params'] : [];
        $children = $props['children'];

        if (isset($params['formAttributes'])) {
            $formAttributes = $params['formAttributes'];
            unset($params['formAttributes']);
        }

        $classes = 'react-like-action';
        if (isset($formAttributes['class'])) {
            $classes .= ' ' . $formAttributes['class'];
            unset($formAttributes['class']);
        }

        return render('form', array_merge(
            [
                'method' => 'post',
                'action' => '',
                'class' => $classes
            ],
            $formAttributes,
            [
                'children' => array_merge(
                    [render('input', [
                        'type' => 'hidden',
                        'name' => 'type',
                        'value' => $type,
                    ])],
                    array_map(function ($key) use ($params) {
                        return render('input', [
                            'type' => 'hidden',
                            'name' => $key,
                            'value' => $params[$key],
                        ]);
                    }, array_keys($params)),
                    is_array($children) ? $children : [$children]
                )
            ]
        ));
    }
}
