<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\BinanceExchangeUtils;
use App\Utils\PriceConversionUtils;
use Binance\API;

class BinanceExchangeService implements ExchangeInterface
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createInstance(): API
    {
        return new API(
            BinanceExchangeUtils::getApiKey(),
            BinanceExchangeUtils::getApiSecret()
        );
    }

    public function getAPIBalance(): array
    {
        $api = $this->createInstance();
        $api->useServerTime();

        return $api->balances();
    }

    public function getBalance(): array
    {
        return $this->walletService->getAllWallets(BinanceExchangeUtils::getDefaultExchangeName());
    }

    public function saveBalance()
    {
        if (!BinanceExchangeUtils::isEnabled()) {
            return;
        }

        $balance = $this->getAPIBalance();

        foreach ($balance as $market => $item) {
            $price = 0;
            try {
                if ((float) $item['available'] > 0) {
                    $price = ('usdt' === strtolower($market)) ? 1 : $this->getPriceOfMarket($market);
                }
            } catch (\Exception $exception) {
                continue;
            }

            $this->walletService->create(
                BinanceExchangeUtils::getDefaultExchangeName(),
                $market,
                $item['available'],
                $item['onOrder'],
                new \DateTime(),
                $price
            );
        }

        $this->walletService->save();
    }

    public function getPriceOfMarket(string $market): float
    {
        $api = $this->createInstance();
        $market = strtoupper($market);
        try {
            try {
                $price = $api->price($market.'USDT');
            } catch (\Exception $exception) {
                try {
                    $price = $api->price($market.'BUSD');
                } catch (\Exception $exception) {
                    $bnbAmount = $api->price($market.'BNB');
                    $price = $api->price('BNBUSDT');
                    $price = $bnbAmount * $price;
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception('Market not found');
        }

        return (float) $price;
    }

    public function aggregateWallets(): array
    {
        return $this->walletService->aggregateWallets(['exchange' => BinanceExchangeUtils::getDefaultExchangeName()]);
    }

    public function removeWallets()
    {
        $this->walletService->removeWallets(BinanceExchangeUtils::getDefaultExchangeName());
    }
}
