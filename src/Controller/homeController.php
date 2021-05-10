<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\DepositType;
use App\Form\WithdrawType;
use App\Service\DepositService;
use App\Service\WithdrawService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class homeController extends AbstractController
{
    private $depositService;
    private $withdrawService;

    public function __construct(DepositService $depositService, WithdrawService $withdrawService)
    {
        $this->depositService = $depositService;
        $this->withdrawService = $withdrawService;
    }

    /**
     * @Route("/", name="crypto_stats")
     */
    public function index(Request $request): Response
    {
        $depositForm = $this->createForm(DepositType::class, null, []);
        $depositForm->handleRequest($request);
        if ($depositForm->isSubmitted() && $depositForm->isValid()) {
            $depositData = $depositForm->getData();
            $this->depositService->add(
                (float) $depositData['amount'],
                $depositData['symbol'],
                $depositData['exchange'],
                $depositData['added']
            );
        }

        $withdrawForm = $this->createForm(WithdrawType::class, null, []);
        $withdrawForm->handleRequest($request);
        if ($withdrawForm->isSubmitted() && $withdrawForm->isValid()) {
            $withdrawData = $withdrawForm->getData();
            $this->withdrawService->add(
                (float) $withdrawData['amount'],
                $withdrawData['symbol'],
                $withdrawData['moved_to'],
                $withdrawData['date']
            );
        }

        return $this->render('/home/template.html.twig', [
            'depositForm' => $depositForm->createView(),
            'withdrawForm' => $withdrawForm->createView()
        ]);
    }
}
