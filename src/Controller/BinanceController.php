<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BinanceExchangeService;
use App\Service\WalletService;
use App\Utils\BinanceExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BinanceController extends AbstractController
{
    private $walletService;
    private $binanceExchangeService;

    public function __construct(
        WalletService $walletService,
        BinanceExchangeService $binanceExchangeService
    ) {
        $this->walletService = $walletService;
        $this->binanceExchangeService = $binanceExchangeService;
    }

    /**
     * @Route("/binance", name="crypto_stats_exchange_binance")
     */
    public function showGlobalStats(): Response
    {
        return $this->render('binance/template.html.twig', [
            'balance' => $this->walletService->aggregateWallets(BinanceExchangeUtils::getDefaultExchangeName()),
        ]);
    }

    /**
     * @Route("/update/wallets/binance", name="crypto_stats_update_binance_wallets", methods={"POST"})
     */
    public function updateWallet(): JsonResponse
    {
        $this->binanceExchangeService->saveBalance();

        return new JsonResponse();
    }
}
