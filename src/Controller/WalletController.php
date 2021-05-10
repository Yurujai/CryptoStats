<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BinanceExchangeService;
use App\Service\BitvavoExchangeService;
use App\Service\GateIOExchangeService;
use App\Service\KukoinExchangeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends AbstractController
{
    private $binanceExchangeService;
    private $bitvavoExchangeService;
    private $kukoinExchangeService;
    private $gateIOExchangeService;

    public function __construct(
        BinanceExchangeService $binanceExchangeService,
        BitvavoExchangeService $bitvavoExchangeService,
        KukoinExchangeService $kukoinExchangeService,
        GateIOExchangeService $gateIOExchangeService
    ) {
        $this->binanceExchangeService = $binanceExchangeService;
        $this->bitvavoExchangeService = $bitvavoExchangeService;
        $this->kukoinExchangeService = $kukoinExchangeService;
        $this->gateIOExchangeService = $gateIOExchangeService;
    }

//    /**
//     * @Route("/last/update/wallets", name="crypto_stats_last_updated_wallets")
//     */
//    public function lastWalletUpdate(): Response
//    {
//        return $this->render('/resources/_block.html.twig', [
//            'headerText' => 'Last updated',
//            'value' => new \DateTime(),
//            'icon' => 'fas fa-calendar-alt',
//        ]);
//    }

    /**
     * @Route("/update/wallets", name="crypto_stats_update_wallets", methods={"POST"})
     */
    public function update(): JsonResponse
    {
        $this->binanceExchangeService->saveBalance();
        $this->bitvavoExchangeService->saveBalance();
        $this->kukoinExchangeService->saveBalance();
        $this->gateIOExchangeService->saveBalance();

        return new JsonResponse();
    }

    /**
     * @Route("/remove/wallets", name="crypto_stats_remove_wallets")
     */
    public function remove(): Response
    {
        $this->binanceExchangeService->removeWallets();
        $this->bitvavoExchangeService->removeWallets();
        $this->kukoinExchangeService->removeWallets();
        $this->gateIOExchangeService->removeWallets();

        return $this->redirectToRoute('crypto_stats');
    }
}
