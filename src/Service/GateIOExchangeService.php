<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\GateioExchangeUtils;
use GateApi\Api\SpotApi;
use GateApi\Configuration;
use GuzzleHttp\Client;

class GateIOExchangeService implements ExchangeInterface
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createInstance(): SpotApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setKey(GateioExchangeUtils::getApiKey())
            ->setSecret(GateioExchangeUtils::getApiSecret());

        return new SpotApi(
            new Client(),
            $config
        );
    }

    public function getAPIBalance()
    {
        $apiInstance = $this->createInstance();

        return $apiInstance->listSpotAccountsWithHttpInfo([]);
    }

    public function getBalance(): array
    {
        return $this->walletService->getAllWallets(GateioExchangeUtils::getDefaultExchangeName());
    }

    public function saveBalance()
    {
        if (!GateioExchangeUtils::isEnabled()) {
            return;
        }

        $balance = $this->getAPIBalance();

        $balance = reset($balance);
        foreach ($balance as $item) {
            try {
                if ((float) $item->getAvailable() > 0) {
                    $price = ('usdt' === strtolower($item->getCurrency())) ? 1 : $this->getPriceOfMarket($item->getCurrency());
                }
            } catch (\Exception $exception) {
                continue;
            }

            $this->walletService->create(
                GateioExchangeUtils::getDefaultExchangeName(),
                $item->getCurrency(),
                $item->getAvailable(),
                $item->getLocked(),
                new \DateTime(),
                $price
            );
        }

        $this->walletService->save();
    }

    public function getPriceOfMarket(string $market): float
    {
        $api = $this->createInstance();
        try {
            $associate_array['currency_pair'] = $market.'_USDT';
            $price = $api->listTickers($associate_array);
        } catch (\Exception $exception) {
            throw new \Exception('Market not found');
        }

        return (float) $price[0]->getLast();
    }

    public function aggregateWallets(): array
    {
        return $this->walletService->aggregateWallets(['exchange' => GateioExchangeUtils::getDefaultExchangeName()]);
    }

    public function removeWallets()
    {
        $this->walletService->removeWallets(GateioExchangeUtils::getDefaultExchangeName());
    }
}
