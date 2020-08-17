<?php

namespace App\Store;

use RenanLiberato\ExposerStore\Persistors\CookiePersistor;
use RenanLiberato\ExposerStore\Persistors\PersistorInterface;

class FileSystemPersistor implements PersistorInterface
{
    /**
     * @var CookiePersistor
     */
    private $cookiePersistor;

    public function __construct(CookiePersistor $cookiePersistor)
    {
        $this->cookiePersistor = $cookiePersistor;
    }

    public function persistState($state)
    {
        $this->cookiePersistor->persistState([
            'ui' => $state['ui'],
            'user_id' => $state['user_id']
        ]);

        if ($state['user_id'] != null) {
            $stateFile = "./data/state_{$state['user_id']}.json";

            file_put_contents($stateFile, json_encode([
                'todos' => $state['todos'],
                'actions_history' => $state['actions_history']
            ], JSON_PRETTY_PRINT));
        }
    }

    public function getPersistedState()
    {
        $stateFromFile = [];
        $stateFromCookie = $this->cookiePersistor->getPersistedState();

        if ($stateFromCookie['user_id'] != null) {
            $stateFile = "./data/state_{$stateFromCookie['user_id']}.json";

            if (file_exists($stateFile)) {
                $stateFromFile = json_decode(file_get_contents($stateFile), true);
            }
        }

        return array_merge(
            $stateFromCookie,
            $stateFromFile
        );
    }
}