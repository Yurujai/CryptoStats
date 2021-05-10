<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\KukoinExchangeUtils;
use App\Utils\PriceConversionUtils;
use KuCoin\SDK\Auth;
use KuCoin\SDK\PrivateApi\Account;
use KuCoin\SDK\PublicApi\Currency;

class KukoinExchangeService implements ExchangeInterface
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createInstance(): Account
    {
        $auth = new Auth(
            KukoinExchangeUtils::getApiKey(),
            KukoinExchangeUtils::getApiSecret(),
            KukoinExchangeUtils::getPassPhrase(),
            Auth::API_KEY_VERSION_V2
        );

        return new Account($auth);
    }

    public function getAPIBalance(): array
    {
        $api = $this->createInstance();

        return $api->getList(['type' => 'trade']);
    }

    public function getBalance(): array
    {
        return $this->walletService->getAllWallets(KukoinExchangeUtils::getDefaultExchangeName());
    }

    public function saveBalance()
    {
        if (!KukoinExchangeUtils::isEnabled()) {
            return;
        }

        $balance = $this->getAPIBalance();

        foreach ($balance as $item) {
            $usdPrice = 0;
            $eurPrice = 0;
            try {
                if ((float) $item['balance'] > 0) {
                    $price = ('usdt' === strtolower($item['currency'])) ? 1 : $this->getPriceOfMarket($item['currency']);
                }
            } catch (\Exception $exception) {
                continue;
            }

            $this->walletService->create(
                KukoinExchangeUtils::getDefaultExchangeName(),
                $item['currency'],
                $item['balance'],
                $item['holds'],
                new \DateTime(),
                $price
            );
        }

        $this->walletService->save();
    }

    public function getPriceOfMarket(string $market): float
    {
        try {
            $currency = new Currency();
            $price = $currency->getPrices('USD', $market);
        } catch (\Exception $exception) {
            throw new \Exception('Market not found');
        }

        return (float) $price[$market];
    }

    public function aggregateWallets(): array
    {
        return $this->walletService->aggregateWallets(['exchange' => KukoinExchangeUtils::getDefaultExchangeName()]);
    }

    public function removeWallets()
    {
        $this->walletService->removeWallets(KukoinExchangeUtils::getDefaultExchangeName());
    }
}
