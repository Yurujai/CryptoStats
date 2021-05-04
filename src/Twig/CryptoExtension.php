<?php

declare(strict_types=1);

namespace App\Twig;

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
}
