<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Investment;
use App\Form\InvestmentType;
use App\Service\InvestmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvestmentController extends AbstractController
{
    private $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    /**
     * @Route("/investment/", name="investment")
     */
    public function show(Request $request): Response
    {
        $investments = $this->investmentService->getAll();

        $investmentForm = $this->createForm(InvestmentType::class, null, []);
        $investmentForm->handleRequest($request);
        if ($investmentForm->isSubmitted() && $investmentForm->isValid()) {
            $investmentData = $investmentForm->getData();
            $this->investmentService->add((float) $investmentData['amount'], $investmentData['currency'], $investmentData['exchange'], $investmentData['added']);
            $investments = $this->investmentService->getAll();
        }

        return $this->render('/investment/template.html.twig', [
            'investments' => $investments,
            'investmentForm' => $investmentForm->createView(),
        ]);
    }

    /**
     * @Route("/investment/remove/{investment}", name="investment_remove_amount")
     */
    public function remove(string $investment): RedirectResponse
    {
        $this->investmentService->remove($investment);

        return $this->redirectToRoute('investment');
    }

    /**
     * @Route("/investment/block", name="investment_block")
     */
    public function block(): Response
    {
        $investment = $this->investmentService->getTotalInvestment();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Investment',
            'value' => $investment,
            'icon' => 'fas fa-dollar-sign'
        ]);
    }
}
