<?php

namespace App\Controller\Admin;

use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuoteWorkflowController extends AbstractController
{
    public function __construct(
        private readonly QuoteRepository $quoteRepo,
        private readonly \EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    #[AdminRoute(path: '/quotes', name: 'quote_pending')]
    public function pendingQuotes(): Response
    {
        return $this->render('admin/quote_index.html.twig', [
            'quotes' => $this->quoteRepo->findPending(),
            'ea' => $this->adminContextProvider->getContext(),
        ]);
    }

    #[AdminRoute(path: '/quotes/{id}/approve', name: 'quote_approve', options: ['methods' => ['POST'], 'requirements' => ['id' => '\d+']])]
    public function approveQuote(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        \App\Service\EmailService $emailService,
        \Psr\Log\LoggerInterface $logger
    ): Response {
        $quote = $this->quoteRepo->find($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if (!$this->isCsrfTokenValid('approve' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote->setStatus('approved');
        $em->flush();

        try {
            $emailService->sendQuoteStatusUpdate($quote);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $logger->error('Email quote update failed: ' . $e->getMessage());
        }

        $this->addFlash('success', 'Devis approuvé.');

        return $this->redirectToRoute('admin_quote_pending');
    }

    #[AdminRoute(path: '/quotes/{id}/reject', name: 'quote_reject', options: ['methods' => ['POST'], 'requirements' => ['id' => '\d+']])]
    public function rejectQuote(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        \App\Service\EmailService $emailService,
        \Psr\Log\LoggerInterface $logger
    ): Response {
        $quote = $this->quoteRepo->find($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if (!$this->isCsrfTokenValid('reject' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote->setStatus('refused');
        $em->flush();

        try {
            $emailService->sendQuoteStatusUpdate($quote);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $logger->error('Email quote update failed: ' . $e->getMessage());
        }

        $this->addFlash('success', 'Devis refusé.');

        return $this->redirectToRoute('admin_quote_pending');
    }
}
