<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\CryptoUtils;

class StatsService
{
    private $walletService;
    private $depositService;
    private $withdrawService;

    public function __construct(
        WalletService $walletService,
        DepositService $depositService,
        WithdrawService $withdrawService
    ) {
        $this->walletService = $walletService;
        $this->depositService = $depositService;
        $this->withdrawService = $withdrawService;
    }

    public function getTotalAmount(bool $addWithdraw = false, ?string $exchange = null): float
    {
        $amount = 0;
        $wallets = $this->walletService->getTotalAmount($exchange);
        foreach ($wallets as $wallet) {
            $amount += $wallet['totalPrice'] + $wallet['inOrderPrice'];
        }

        if($addWithdraw) {
            $amount += $this->withdrawService->getTotal();
        }

        return $amount;
    }

    public function calculateProfit(): float
    {
        $amount = $this->getTotalAmount(true);

        $deposit = $this->depositService->getTotal();

        if($deposit == 0) {
            return 0;
        }

        return ($amount - $deposit) / $deposit * 100;
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
        $global = $this->getTotalAmount(false);
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $key => $wallet) {
            $wallets[$key]['percentage'] = (($wallet['totalUSD'] + $wallet['inOrderUSD']) * 100) / $global;
        }

        return $wallets;
    }
}
