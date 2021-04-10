<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BinanceExchangeService;
use App\Service\BitvavoExchangeService;
use App\Service\CoinbaseExchangeService;
use App\Service\GateIOExchangeService;
use App\Service\KukoinExchangeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends AbstractController
{
    private $binanceExchangeService;
    private $bitvavoExchangeService;
    private $coinbaseExchangeService;
    private $kukoinExchangeService;
    private $gateIOExchangeService;

    public function __construct(
        BinanceExchangeService $binanceExchangeService,
        BitvavoExchangeService $bitvavoExchangeService,
        CoinbaseExchangeService $coinbaseExchangeService,
        KukoinExchangeService $kukoinExchangeService,
        GateIOExchangeService $gateIOExchangeService
    ) {
        $this->binanceExchangeService = $binanceExchangeService;
        $this->bitvavoExchangeService = $bitvavoExchangeService;
        $this->coinbaseExchangeService = $coinbaseExchangeService;
        $this->kukoinExchangeService = $kukoinExchangeService;
        $this->gateIOExchangeService = $gateIOExchangeService;
    }

    /**
     * @Route("/update/wallets", name="crypto_stats_update_wallets", methods={"POST"})
     */
    public function update(): JsonResponse
    {
        $this->binanceExchangeService->saveBalance();
        $this->bitvavoExchangeService->saveBalance();
        $this->coinbaseExchangeService->saveBalance();
        $this->kukoinExchangeService->saveBalance();
        $this->gateIOExchangeService->saveBalance();

        return new JsonResponse();
    }
}
