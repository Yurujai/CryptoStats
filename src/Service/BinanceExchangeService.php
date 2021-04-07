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

    public function createInstance()
    {
        return new API(
            BinanceExchangeUtils::getApiKey(),
            BinanceExchangeUtils::getApiSecret()
        );
    }

    public function getAPIBalance(): array
    {
        $api = $this->createInstance();

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
            $usdPrice = 0;
            $eurPrice = 0;
            try {
                if ((float) $item['available'] > 0) {
                    if ('usdt' === strtolower($market)) {
                        $usdPrice = 1;
                        $eurPrice = PriceConversionUtils::getEURFromUSD((float) 1);
                    } else {
                        $usdPrice = (float) $this->getPriceOfMarket($market);
                        $eurPrice = PriceConversionUtils::getEURFromUSD($usdPrice);
                    }
                }
            } catch (\Exception $exception) {
                continue;
            }

            $this->walletService->create(
                BinanceExchangeUtils::getDefaultExchangeName(),
                $market,
                $item['available'],
                $item['onOrder'],
                $eurPrice,
                $usdPrice
            );
        }

        $this->walletService->save();
    }

    public function getPriceOfMarket(string $market): float
    {
        $api = $this->createInstance();
        try {
            $price = $api->price($market.'USDT');
        } catch (\Exception $exception) {
            throw new \Exception('Market not found');
        }

        return (float) $price;
    }
}
