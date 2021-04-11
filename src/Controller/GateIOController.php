<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GateIOExchangeService;
use App\Service\WalletService;
use App\Utils\BinanceExchangeUtils;
use App\Utils\GateioExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GateIOController extends AbstractController
{
    private $walletService;
    private $gateIOExchangeService;

    public function __construct(
        WalletService $walletService,
        GateIOExchangeService $gateIOExchangeService
    ) {
        $this->walletService = $walletService;
        $this->gateIOExchangeService = $gateIOExchangeService;
    }

    /**
     * @Route("/gateio", name="crypto_stats_exchange_gateio")
     */
    public function balance(): Response
    {
        return $this->render('binance/template.html.twig', [
            'balance' => $this->walletService->aggregateWallets(BinanceExchangeUtils::getDefaultExchangeName()),
            'exchange' => GateioExchangeUtils::getDefaultExchangeName(),
        ]);
    }

    /**
     * @Route("/update/wallets/gateio", name="crypto_stats_update_gateio_wallets", methods={"POST"})
     */
    public function updateWallet(): JsonResponse
    {
        $this->gateIOExchangeService->saveBalance();

        return new JsonResponse();
    }
}
