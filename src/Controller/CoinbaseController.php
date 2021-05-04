<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CoinbaseExchangeService;
use App\Utils\CoinbaseExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoinbaseController extends AbstractController
{
    private $coinbaseExchangeService;

    public function __construct(CoinbaseExchangeService $coinbaseExchangeService)
    {
        $this->coinbaseExchangeService = $coinbaseExchangeService;
    }

    /**
     * @Route("/coinbase", name="crypto_stats_exchange_coinbase")
     */
    public function balance(): Response
    {
        return $this->render('exchange/template.html.twig', [
            'balance' => $this->coinbaseExchangeService->aggregateWallets(),
            'exchange' => CoinbaseExchangeUtils::getDefaultExchangeName(),
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
