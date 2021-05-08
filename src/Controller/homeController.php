<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\InvestmentType;
use App\Form\WithdrawType;
use App\Service\InvestmentService;
use App\Service\WithdrawService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class homeController extends AbstractController
{
    private $investmentService;
    private $withdrawService;

    public function __construct(InvestmentService $investmentService, WithdrawService $withdrawService)
    {
        $this->investmentService = $investmentService;
        $this->withdrawService = $withdrawService;
    }

    /**
     * @Route("/", name="crypto_stats")
     */
    public function index(Request $request): Response
    {
        $investmentForm = $this->createForm(InvestmentType::class, null, []);
        $investmentForm->handleRequest($request);
        if ($investmentForm->isSubmitted() && $investmentForm->isValid()) {
            $investmentData = $investmentForm->getData();
            $this->investmentService->add(
                (float) $investmentData['amount'],
                $investmentData['currency'],
                $investmentData['exchange'],
                $investmentData['added']
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
                $withdrawData['comment'],
                $withdrawData['date']
            );
        }

        return $this->render('/home/template.html.twig', [
            'investmentForm' => $investmentForm->createView(),
            'withdrawForm' => $withdrawForm->createView()
        ]);
    }
}
