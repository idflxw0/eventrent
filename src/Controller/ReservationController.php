<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Notification;
use App\Entity\Reservation;
use App\Entity\ReservationLine;
use App\Form\ReservationType;
use App\Repository\EquipmentRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\ReservationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger) {}

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
        ReservationRepository $reservationRepo,
        \App\Service\EmailService $emailService,
        \App\Service\WeatherService $weatherService,
    ): Response {
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());

        if ($request->isMethod('GET')) {
            $equipId = $request->query->getInt('equipment');
            if ($equipId > 0) {
                $preselected = $equipRepo->find($equipId);
                if ($preselected instanceof \App\Entity\Equipment && $preselected->getAvailabilityStatus() === \App\Entity\Equipment::STATUS_AVAILABLE) {
                    $line = new ReservationLine();
                    $line->setEquipment($preselected);
                    $line->setQuantity(1);
                    $line->setUnitPricePerDay($preselected->getDailyPrice());
                    $line->setReservation($reservation);
                    $reservation->addLine($line);
                }
            }

            // Restore fields after conflict redirect
            if ($request->query->get('start')) {
                try { $reservation->setStartDate(new \DateTimeImmutable($request->query->get('start'))); } catch (\Exception) {}
            }
            if ($request->query->get('end')) {
                try { $reservation->setEndDate(new \DateTimeImmutable($request->query->get('end'))); } catch (\Exception) {}
            }
            if ($request->query->get('city')) {
                $reservation->setEventCity($request->query->get('city'));
            }
            if ($request->query->get('venue')) {
                $reservation->setVenueType($request->query->get('venue'));
            }
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipmentIds = array_map(
                fn($line) => $line->getEquipment()->getId(),
                $reservation->getLines()->toArray()
            );
            $conflicts = $reservationRepo->findConflictingEquipment(
                $equipmentIds,
                $reservation->getStartDate(),
                $reservation->getEndDate()
            );

            if (!empty($conflicts)) {
                $names = implode(', ', array_map(fn($e) => $e->getName(), $conflicts));
                $this->addFlash('danger', "Équipement(s) déjà réservé(s) sur cette période : {$names}.");

                $params = [
                    'start' => $reservation->getStartDate()?->format('Y-m-d'),
                    'end'   => $reservation->getEndDate()?->format('Y-m-d'),
                    'city'  => $reservation->getEventCity(),
                    'venue' => $reservation->getVenueType(),
                ];
                $firstEquip = $reservation->getLines()->first()?->getEquipment();
                if ($firstEquip) {
                    $params['equipment'] = $firstEquip->getId();
                }
                $category = $form->get('category')->getData();
                if ($category) {
                    $params['category'] = $category->getId();
                }

                return $this->redirectToRoute('reservation_new', array_filter($params));
            }

            $days = max(1, (int) $reservation->getStartDate()->diff($reservation->getEndDate())->days);
            $total = 0.0;

            foreach ($reservation->getLines() as $line) {
                $equip = $equipRepo->find($line->getEquipment()->getId());
                if ($equip) {
                    $line->setEquipment($equip);
                    $line->setUnitPricePerDay($equip->getDailyPrice());
                    $line->setReservation($reservation);
                    $total += (float) $line->getUnitPricePerDay() * $line->getQuantity() * $days;
                }
            }

            $reservation->setTotalAmount(number_format($total, 2, '.', ''));

            $invoice = new Invoice();
            $invoice->setReservation($reservation);
            $invoice->setNumber('INV-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT));
            $invoice->setAmount(number_format($total, 2, '.', ''));
            $invoice->setDueDate((new \DateTimeImmutable())->modify('+30 days'));

            $em->persist($reservation);
            $em->persist($invoice);
            $em->flush();

            $weather = null;
            if ($reservation->getVenueType() === 'outdoor') {
                $weather = $weatherService->getForecast(
                    $reservation->getEventCity(),
                    $reservation->getStartDate()
                );
                if ($weather) {
                    $reservation->setWeatherForecast($weather);
                    $em->flush();
                }
            }

            try {
                $emailService->sendReservationConfirmation($reservation, $weather);
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('Email confirmation failed: ' . $e->getMessage());
            }

            $notif = new Notification();
            $notif->setUser($reservation->getUser());
            $notif->setMessage(sprintf('Votre réservation à %s (%s – %s) est confirmée.', $reservation->getEventCity(), $reservation->getStartDate()->format('d/m/Y'), $reservation->getEndDate()->format('d/m/Y')));
            $notif->setType('reservation_confirmed');
            $em->persist($notif);
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

        $notif = new Notification();
        $notif->setUser($reservation->getUser());
        $notif->setMessage(sprintf('Votre réservation à %s a été annulée.', $reservation->getEventCity()));
        $notif->setType('reservation_cancelled');
        $em->persist($notif);
        $em->flush();

        $this->addFlash('success', 'Réservation annulée.');

        return $this->redirectToRoute('reservation_index');
    }
}
