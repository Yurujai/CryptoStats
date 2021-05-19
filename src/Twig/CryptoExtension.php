<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\MarketService;
use App\Service\StatsService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CryptoExtension extends AbstractExtension
{
    private $statsService;
    private $marketService;

    public function __construct(
        StatsService $statsService,
        MarketService $marketService
    ) {
        $this->statsService = $statsService;
        $this->marketService = $marketService;
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
            new TwigFunction('info_of_asset', [$this, 'getInfoOfAsset']),
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
            'percent' => ($totalAmount > 0) ? ($amountOfFiat * 100) / $totalAmount : 0,
            'number' => $this->getNumberOfFiat(),
            'amount' => $amountOfFiat,
        ];
    }

    public function getDataOfCrypto(): array
    {
        $totalAmount = $this->statsService->getTotalAmount(true);
        $amountOfCrypto = $this->statsService->amountOfCrypto();

        return [
            'percent' => ($totalAmount > 0) ? ($amountOfCrypto * 100) / $totalAmount : 0,
            'number' => $this->getNumberOfCrypto(),
            'amount' => $amountOfCrypto,
        ];
    }

    public function getInfoOfAsset(string $symbol)
    {
        return $this->marketService->getInfoOfAsset($symbol);
    }

    public function getDataOfStable(): array
    {
        $totalAmount = $this->statsService->getTotalAmount(true);
        $amountOfSable = $this->statsService->amountOfStable();

        return [
            'percent' => ($totalAmount > 0) ? ($amountOfSable * 100) / $totalAmount : 0,
            'number' => $this->getNumberOfStable(),
            'amount' => $amountOfSable,
        ];
    }
}
