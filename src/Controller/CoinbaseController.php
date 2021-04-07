<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CoinbaseExchangeService;
use App\Service\WalletService;
use App\Utils\CoinbaseExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoinbaseController extends AbstractController
{
    private $walletService;
    private $coinbaseExchangeService;

    public function __construct(
        WalletService $walletService,
        CoinbaseExchangeService $coinbaseExchangeService
    ) {
        $this->walletService = $walletService;
        $this->coinbaseExchangeService = $coinbaseExchangeService;
    }

    /**
     * @Route("/coinbase", name="crypto_stats_exchange_coinbase")
     */
    public function showGlobalStats(): Response
    {
        return $this->render('coinbase/template.html.twig', [
            'balance' => $this->walletService->aggregateWallets(CoinbaseExchangeUtils::getDefaultExchangeName()),
        ]);
    }

    /**
     * @Route("/update/wallets/coinbase", name="crypto_stats_update_coinbase_wallets", methods={"POST"})
     */
    public function updateWallet(): JsonResponse
    {
        $this->coinbaseExchangeService->saveBalance();

        return new JsonResponse();
    }
}
