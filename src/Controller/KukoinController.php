<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\KukoinExchangeService;
use App\Utils\KukoinExchangeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KukoinController extends AbstractController
{
    private $kukoinExchangeService;

    public function __construct(KukoinExchangeService $kukoinExchangeService)
    {
        $this->kukoinExchangeService = $kukoinExchangeService;
    }

    /**
     * @Route("/kukoin", name="crypto_stats_exchange_kukoin")
     */
    public function balance(): Response
    {
        return $this->render('kukoin/template.html.twig', [
            'balance' => $this->kukoinExchangeService->aggregateWallets(),
            'exchange' => KukoinExchangeUtils::getDefaultExchangeName(),
        ]);
    }

    /**
     * @Route("/update/wallets/kukoin", name="crypto_stats_update_kukoin_wallets", methods={"POST"})
     */
    public function updateWallet(): JsonResponse
    {
        $this->kukoinExchangeService->saveBalance();

        return new JsonResponse();
    }
}
