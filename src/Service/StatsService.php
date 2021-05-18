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

    public function getTotalAmountByExchange(): array
    {
        $exchanges = $this->walletService->getExchanges();
        $data = [];
        foreach($exchanges as $exchange) {
            $data[$exchange] =$this->getTotalAmount(false, $exchange);
        }

        return $data;
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
        $criteria = ['symbol' => ['$in' => CryptoUtils::getListOfFiatCoin()]];
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $wallet) {
            $amount += $wallet['totalPrice'] + $wallet['inOrderPrice'];
        }

        return $amount;
    }

    public function amountOfStable(): float
    {
        $amount = 0;
        $criteria = ['symbol' => ['$in' => CryptoUtils::getListOfStableCoin()]];
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $wallet) {
            $amount += $wallet['totalPrice'] + $wallet['inOrderPrice'];
        }

        return $amount;
    }

    public function amountOfCrypto(): float
    {
        $amount = 0;
        $excludeSymbols = array_merge(CryptoUtils::getListOfStableCoin(), CryptoUtils::getListOfFiatCoin());
        $criteria = ['symbol' => ['$nin' => $excludeSymbols]];
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $wallet) {
            $amount += $wallet['totalPrice'] + $wallet['inOrderPrice'];
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
        $fiatCoins = CryptoUtils::getListOfFiatCoin();
        $stableCoins = CryptoUtils::getListOfStableCoin();
        $criteria = ['symbol' => ['$nin' => array_merge($fiatCoins, $stableCoins)]];
        $global = $this->getTotalAmount(true);
        $wallets = $this->walletService->aggregateWallets($criteria);
        foreach ($wallets as $key => $wallet) {
            $wallets[$key]['percentage'] = (($wallet['totalPrice'] + $wallet['inOrderPrice']) * 100) / $global;
            $wallets[$key]['total'] = (float) number_format(($wallet['totalPrice'] + $wallet['inOrderPrice']), 2);
        }

        $keys = array_column($wallets, 'percentage');
        array_multisort($keys, SORT_DESC, $wallets);

        return $wallets;
    }

    public function getDepositAmount(): float
    {
        return $this->depositService->getTotal();
    }
    public function getWithdrawAmount(): float
    {
        return $this->withdrawService->getTotal();
    }

}
