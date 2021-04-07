<?php

declare(strict_types=1);

namespace App\Utils;

class CoinbaseExchangeUtils
{
    public static function getDefaultExchangeName(): string
    {
        return 'coinbase';
    }

    public static function isEnabled(): bool
    {
        if (!isset($_SERVER['COINBASE_ENABLED'])) {
            throw new \Exception('Var '.'COINBASE_ENABLED'.' not found');
        }

        return 'true' === $_SERVER['COINBASE_ENABLED'];
    }

    public static function getApiKey(): string
    {
        if (!isset($_SERVER['COINBASE_API_KEY'])) {
            throw new \Exception('Var '.'COINBASE_API_KEY'.' not found');
        }

        return $_SERVER['COINBASE_API_KEY'];
    }

    public static function getApiSecret(): string
    {
        if (!isset($_SERVER['COINBASE_API_SECRET'])) {
            throw new \Exception('Var '.'COINBASE_API_SECRET'.' not found');
        }

        return $_SERVER['COINBASE_API_SECRET'];
    }
}
