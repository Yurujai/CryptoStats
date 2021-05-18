<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\BinanceExchangeService;
use App\Service\StatsService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CryptoExtension extends AbstractExtension
{
    private $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('number_of_fiat', [$this, 'getNumberOfFiat']),
            new TwigFunction('number_of_stable', [$this, 'getNumberOfStable']),
            new TwigFunction('number_of_crypto', [$this, 'getNumberOfCrypto']),
            new TwigFunction('data_of_fiat', [$this, 'getDataOfFiat']),
            new TwigFunction('data_of_stable', [$this, 'getDataOfStable']),
            new TwigFunction('data_of_crypto', [$this, 'getDataOfCrypto']),
        ];
    }

    public function getNumberOfFiat(): int
    {
        return $this->statsService->getNumberOfFiat();
    }

    public function getNumberOfStable(): int
    {
        return $this->statsService->getNumberOfStable();
    }

    public function getNumberOfCrypto(): int
    {
        return $this->statsService->getNumberOfCrypto();
    }

    public function getDataOfFiat(): array
    {
        $totalAmount = $this->statsService->getTotalAmount(true);
        $amountOfFiat = $this->statsService->amountOfFiat();

        return [
            'percent' => ($amountOfFiat * 100) / $totalAmount,
            'number' => $this->getNumberOfFiat(),
            'amount' => $amountOfFiat,
        ];
    }

    public function getDataOfCrypto(): array
    {
        $totalAmount = $this->statsService->getTotalAmount(true);
        $amountOfCrypto = $this->statsService->amountOfCrypto();

        return [
            'percent' =>  ($amountOfCrypto * 100) / $totalAmount,
            'number' => $this->getNumberOfCrypto(),
            'amount' => $amountOfCrypto,
        ];
    }


    public function getDataOfStable(): array
    {
        $totalAmount = $this->statsService->getTotalAmount(true);
        $amountOfSable = $this->statsService->amountOfStable();

        return [
            'percent' => ($amountOfSable * 100) / $totalAmount,
            'number' => $this->getNumberOfStable(),
            'amount' => $amountOfSable,
        ];
    }
}
