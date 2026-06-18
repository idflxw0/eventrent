<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Reservation;
use App\Entity\ReservationLine;
use App\Form\ReservationType;
use App\Repository\EquipmentRepository;
use App\Repository\ReservationRepository;
use App\Security\Voter\ReservationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'reservation_index')]
    public function index(Request $request, ReservationRepository $repo): Response
    {
        $status = $request->query->get('status');
        $reservations = $repo->findByUser($this->getUser()->getId(), $status);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
            'activeStatus' => $status,
        ]);
    }

    #[Route('/reservations/new', name: 'reservation_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        EquipmentRepository $equipRepo,
    ): Response {
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = max(1, (int) $reservation->getStartDate()->diff($reservation->getEndDate())->days);
            $total = '0';

            foreach ($reservation->getLines() as $line) {
                // re-fetch equipment to get current price
                $equip = $equipRepo->find($line->getEquipment()->getId());
                if ($equip) {
                    $line->setEquipment($equip);
                    $line->setUnitPricePerDay($equip->getDailyPrice());
                    $line->setReservation($reservation);
                    $lineTotal = bcmul($line->getUnitPricePerDay(), (string) $line->getQuantity(), 2);
                    $lineTotal = bcmul($lineTotal, (string) $days, 2);
                    $total = bcadd($total, $lineTotal, 2);
                }
            }

            $reservation->setTotalAmount($total);

            $invoice = new Invoice();
            $invoice->setReservation($reservation);
            $invoice->setNumber('INV-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT));
            $invoice->setAmount($total);
            $invoice->setDueDate((new \DateTimeImmutable())->modify('+30 days'));

            $em->persist($reservation);
            $em->persist($invoice);
            $em->flush();

            $this->addFlash('success', 'Réservation créée avec succès.');

            return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reservations/{id}', name: 'reservation_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ReservationRepository $repo): Response
    {
        $reservation = $repo->findOneWithRelations($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation introuvable.');
        }

        if ($reservation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $days = max(1, (int) $reservation->getStartDate()->diff($reservation->getEndDate())->days);

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
            'days' => $days,
            'canCancel' => $this->isGranted(ReservationVoter::CANCEL, $reservation),
        ]);
    }

    #[Route('/reservations/{id}/cancel', name: 'reservation_cancel', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function cancel(int $id, Request $request, ReservationRepository $repo, EntityManagerInterface $em): Response
    {
        $reservation = $repo->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation introuvable.');
        }

        if (!$this->isCsrfTokenValid('cancel' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $this->denyAccessUnlessGranted(ReservationVoter::CANCEL, $reservation);

        $reservation->setStatus('cancelled');
        $em->flush();

        $this->addFlash('success', 'Réservation annulée.');

        return $this->redirectToRoute('reservation_index');
    }
}
