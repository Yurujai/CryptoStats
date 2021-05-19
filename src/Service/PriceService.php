<?php

declare(strict_types=1);

namespace App\Service;

class PriceService
{
    private $binanceExchangeService;

    public function __construct(BinanceExchangeService $binanceExchangeService)
    {
        $this->binanceExchangeService = $binanceExchangeService;
    }

    public function getPriceOfSymbol(string $symbol): float
    {
        return $this->binanceExchangeService->getPriceOfMarket($symbol);
    }
}
