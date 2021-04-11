<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BitvavoExchangeService;
use App\Service\WalletService;
use App\Utils\BitvavoExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BitvavoController extends AbstractController
{
    private $walletService;
    private $bitvavoExchangeService;

    public function __construct(
        WalletService $walletService,
        BitvavoExchangeService $bitvavoExchangeService
    ) {
        $this->walletService = $walletService;
        $this->bitvavoExchangeService = $bitvavoExchangeService;
    }

    /**
     * @Route("/bitvavo", name="crypto_stats_exchange_bitvavo")
     */
    public function balance(): Response
    {
        return $this->render('exchange/template.html.twig', [
            'balance' => $this->walletService->aggregateWallets(BitvavoExchangeUtils::getDefaultExchangeName()),
            'exchange' => BitvavoExchangeUtils::getDefaultExchangeName(),
        ]);
    }

    /**
     * @Route("/update/wallets/bitvavo", name="crypto_stats_update_bitvavo_wallets", methods={"POST"})
     */
    public function updateWallet(): JsonResponse
    {
        $this->bitvavoExchangeService->saveBalance();

        return new JsonResponse();
    }
}
