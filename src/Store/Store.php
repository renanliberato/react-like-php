<?php

namespace App\Store;

class Store
{
    public const JWT_KEY = '!#OIGJ!#$F12ofij123fo123FJ!@3';
    public const INITIAL_STATE = [
        'todos' => [],
        'ui' => [
            'editing_todo' => false
        ],
        'actions_history' => []
    ];

    public static function create()
    {
        $store = self::INITIAL_STATE;

        if (!isset($_COOKIE[TOKEN_COOKIE_NAME])) {
            $jwt = \Firebase\JWT\JWT::encode($store, self::JWT_KEY);

            setcookie(TOKEN_COOKIE_NAME, $jwt);
        } else {
            $store = json_decode(json_encode((array)\Firebase\JWT\JWT::decode($_COOKIE[TOKEN_COOKIE_NAME], self::JWT_KEY, ['HS256'])), true);
        }

        return $store;
    }
}
