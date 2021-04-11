<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvestmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function show(): Response
    {
        $investments = $this->investmentService->getAll();

        return $this->render('/investment/template.html.twig', [
            'investments' => $investments,
        ]);
    }

    /**
     * @Route("/investment/add/{amount}/{currency}", name="add_investment_amount")
     */
    public function add(string $amount, string $currency): RedirectResponse
    {
        $this->investmentService->addInvestment((float) $amount, $currency);

        return $this->redirectToRoute('crypto_stats');
    }
}
