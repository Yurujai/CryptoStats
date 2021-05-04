<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\CryptoUtils;

class StatsService
{
    private $walletService;
    private $investmentService;

    public function __construct(WalletService $walletService, InvestmentService $investmentService)
    {
        $this->walletService = $walletService;
        $this->investmentService = $investmentService;
    }

    public function getTotalAmount(?string $exchange = null): float
    {
        $amount = 0;
        $wallets = $this->walletService->getTotalAmount($exchange);
        foreach ($wallets as $wallet) {
            $amount += $wallet['total'] + $wallet['inOrderUSD'];
        }

        return $amount;
    }

    public function calculateProfit(): float
    {
        $amount = $this->getTotalAmount();

        $investment = $this->investmentService->getTotalInvestment();

        return ($amount - $investment) / $investment * 100;
    }

    public function countCrypto(): int
    {
        return count($this->walletService->aggregateWallets());
    }

    public function amountOfFiat(): float
    {
        $amount = 0;
        $wallets = $this->walletService->aggregateWallets();
        foreach ($wallets as $wallet) {
            $amount += $wallet['totalUSD'] + $wallet['inOrderUSD'];
        }

        return $amount;
    }

    public function getNumberOfFiat(): int
    {
        $criteria = ['symbol' => ['$in' => CryptoUtils::getListOfFiatCoin()]];
        $elements = $this->walletService->getNumberOfCrypto($criteria);

        return count($elements);
    }

    public function getNumberOfStable(): int
    {
        $criteria = ['symbol' => ['$in' => CryptoUtils::getListOfStableCoin()]];
        $elements = $this->walletService->getNumberOfCrypto($criteria);

        return count($elements);
    }

    public function getNumberOfCrypto(): int
    {
        $listOfStableCoins = CryptoUtils::getListOfStableCoin();
        $listOfFiatCoins = CryptoUtils::getListOfFiatCoin();
        $criteria = ['symbol' => ['$nin' => array_merge($listOfStableCoins, $listOfFiatCoins)]];
        $elements = $this->walletService->getNumberOfCrypto($criteria);

        return count($elements);
    }

    public function getListOfCrypto(): array
    {
        $criteria = [];
        $global = $this->getTotalAmount();
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $key => $wallet) {
            $wallets[$key]['percentage'] = (($wallet['totalUSD'] + $wallet['inOrderUSD']) * 100) / $global;
        }

        return $wallets;
    }
}
