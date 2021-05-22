<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExchangeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_exchange_enabled', [$this, 'isExchangeEnabled']),
            new TwigFunction('info_of_exchange', [$this, 'getInfoOfExchange']),
        ];
    }

    public function isExchangeEnabled(string $exchange): bool
    {
        $className = 'App\Utils\\'.ucfirst($exchange).'ExchangeUtils';

        return $className::isEnabled();
    }

    public function getInfoOfExchange(string $exchange)
    {
    }
}
