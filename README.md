# react-like-php

This project is a simple proof of concept applying some ReactJS components on creating PHP UIs and state management.

I need to extract the code to separate files, but I think it already serves its purpose =P

## Component example

```php
function row($children)
{
    return render('div', [
        'attributes' => [
            'class' => 'row',
        ],
        'children' => $children
    ]);
}
```

## State reducer
All client actions are sent following a redux-action like structure:
```json
{
    "type": "ADD_TODO",
    "name": "Item 1"
}
```

When reaching the server, the action is sent to a reducer that process the new state:
```php
function reducer($state, $action)
{
    switch ($action['type']) {
        case 'ADD_TODO':
            $state['todos'][] = ['id' => count($state['todos']) + 1, 'name' => $action['name']];
            return $state;
        case 'EDIT_TODO_SHOW':
            $state['ui']['editing_todo'] = $action['id'];
            return $state;
        case 'REMOVE_TODO':
            $state['todos'] = array_filter($state['todos'], function ($i) use ($action) {
                return $i['id'] != $action['id'];
            });
            return $state;
        case 'EDIT_TODO_SAVE':
            foreach ($state['todos'] as $key => $todo) {
                if ($todo['id'] == $action['id']) {
                    $state['todos'][$key]['name'] = $action['name'];
                }
            }
            $state['ui']['editing_todo'] = false;

            return $state;
        default:
            return $state;
    }
}
```