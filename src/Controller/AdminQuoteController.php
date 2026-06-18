<?php

namespace App\Controller;

use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminQuoteController extends AbstractController
{
    #[Route('/admin/quotes', name: 'admin_quote_index')]
    public function index(QuoteRepository $repo): Response
    {
        return $this->render('admin/quote_index.html.twig', [
            'quotes' => $repo->findPending(),
        ]);
    }

    #[Route('/admin/quotes/{id}/approve', name: 'admin_quote_approve', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function approve(int $id, Request $request, QuoteRepository $repo, EntityManagerInterface $em): Response
    {
        $quote = $repo->find($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if (!$this->isCsrfTokenValid('approve' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote->setStatus('approved');
        $em->flush();

        $this->addFlash('success', 'Devis approuvé.');

        return $this->redirectToRoute('admin_quote_index');
    }

    #[Route('/admin/quotes/{id}/reject', name: 'admin_quote_reject', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function reject(int $id, Request $request, QuoteRepository $repo, EntityManagerInterface $em): Response
    {
        $quote = $repo->find($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if (!$this->isCsrfTokenValid('reject' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote->setStatus('refused');
        $em->flush();

        $this->addFlash('success', 'Devis refusé.');

        return $this->redirectToRoute('admin_quote_index');
    }
}
