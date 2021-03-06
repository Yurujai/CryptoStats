<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\BitvavoExchangeUtils;
use App\Utils\PriceConversionUtils;
use Bitvavo;

class BitvavoExchangeService implements ExchangeInterface
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createInstance(): Bitvavo
    {
        return new Bitvavo([
            'APIKEY' => BitvavoExchangeUtils::getApiKey(),
            'APISECRET' => BitvavoExchangeUtils::getApiSecret(),
            'ACCESSWINDOW' => 60000,
        ]);
    }

    public function getAPIBalance()
    {
        $data = $this->createInstance()->balance([]);
        $available = array_column($data, 'symbol');
        array_multisort($data, $available, SORT_DESC);

        return $data;
    }

    public function getBalance(): array
    {
        return $this->walletService->getAllWallets(BitvavoExchangeUtils::getDefaultExchangeName());
    }

    public function saveBalance()
    {
        if (!BitvavoExchangeUtils::isEnabled()) {
            return;
        }

        $balance = $this->getAPIBalance();

        foreach ($balance as $item) {
            try {
                $price = ('EUR' === $item['symbol']) ?
                    PriceConversionUtils::getUSDFromEUR(1) :
                    PriceConversionUtils::getUSDFromEUR($this->getPriceOfMarket($item['symbol']));
            } catch (\Exception $exception) {
                continue;
            }

            $this->walletService->create(
                BitvavoExchangeUtils::getDefaultExchangeName(),
                $item['symbol'],
                $item['available'],
                $item['inOrder'],
                new \DateTime(),
                $price
            );
        }
        $this->walletService->save();
    }

    public function getPriceOfMarket(string $symbol): float
    {
        $api = $this->createInstance();
        try {
            $price = $api->tickerPrice(['market' => $symbol.'-EUR']);
        } catch (\Exception $exception) {
            throw new \Exception('Market not found');
        }

        return (float) $price['price'];
    }

    public function aggregateWallets(): array
    {
        return $this->walletService->aggregateWallets(['exchange' => BitvavoExchangeUtils::getDefaultExchangeName()]);
    }

    public function removeWallets()
    {
        $this->walletService->removeWallets(BitvavoExchangeUtils::getDefaultExchangeName());
    }
}
