<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\CoinbaseExchangeUtils;
use App\Utils\PriceConversionUtils;
use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;

class CoinbaseExchangeService implements ExchangeInterface
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createInstance(): Client
    {
        $configuration = Configuration::apiKey(
            CoinbaseExchangeUtils::getApiKey(),
            CoinbaseExchangeUtils::getApiSecret()
        );
        $client = Client::create($configuration);
        $client->enableActiveRecord();

        return $client;
    }

    public function getAPIBalance()
    {
        $client = $this->createInstance();

        return $client->getAccounts(['limit' => 100]);
    }

    public function getBalance(): array
    {
        return $this->walletService->getAllWallets(CoinbaseExchangeUtils::getDefaultExchangeName());
    }

    public function saveBalance()
    {
        if (!CoinbaseExchangeUtils::isEnabled()) {
            return;
        }

        $balance = $this->getAPIBalance();

        foreach ($balance as $item) {
            try {
                if ('EUR' === $item->getNativeBalance()->getCurrency()) {
                    $eurPrice = (float) $item->getNativeBalance()->getAmount();
                    $usdPrice = PriceConversionUtils::getUSDFromEUR($eurPrice);
                } else {
                    $usdPrice = (float) $item->getNativeBalance()->getAmount();
                    $eurPrice = PriceConversionUtils::getEURFromUSD((float) $usdPrice);
                }
            } catch (\Exception $exception) {
                continue;
            }
            $this->walletService->create(
                CoinbaseExchangeUtils::getDefaultExchangeName(),
                $item->getBalance()->getCurrency(),
                $item->getBalance()->getAmount(),
                '0',
                $eurPrice,
                $usdPrice
            );
        }
        $this->walletService->save();
    }

    public function aggregateWallets(): array
    {
        return $this->walletService->aggregateWallets(['exchange' => CoinbaseExchangeUtils::getDefaultExchangeName()]);
    }

    public function removeWallets()
    {
        $this->walletService->removeWallets(CoinbaseExchangeUtils::getDefaultExchangeName());
    }
}
