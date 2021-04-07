<?php

declare(strict_types=1);

namespace App\Utils;

class PriceConversionUtils
{
    public static function getEURFromUSD(float $usd): float
    {
        return $usd * 0.85;
    }

    public static function getUSDFromEUR(float $eur): float
    {
        return $eur * 1.17;
    }
}
