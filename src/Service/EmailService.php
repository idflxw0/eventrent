<?php

namespace App\Service;

use App\Entity\Maintenance;
use App\Entity\Quote;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    private const SENDER = 'noreply@eventrent.com';

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function sendRegistrationConfirmation(User $user): void
    {
        $catalogueUrl = $this->urlGenerator->generate('catalog_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from(self::SENDER)
            ->to($user->getEmail())
            ->subject('Bienvenue sur EventRent !')
            ->html(sprintf(
                '<h1>Bienvenue %s !</h1><p>Votre compte a été créé avec succès.</p><p>Vous pouvez dès maintenant consulter notre <a href="%s">catalogue</a> et réserver du matériel.</p>',
                htmlspecialchars($user->getFirstName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($catalogueUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            ));

        $this->mailer->send($email);
    }

    public function sendReservationConfirmation(Reservation $reservation, ?string $weather = null): void
    {
        $user = $reservation->getUser();
        $lines = '';
        foreach ($reservation->getLines() as $line) {
            $lines .= sprintf(
                '<li>%s × %d — %s €/jour</li>',
                htmlspecialchars($line->getEquipment()->getName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $line->getQuantity(),
                htmlspecialchars((string) $line->getUnitPricePerDay(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );
        }

        $weatherBlock = '';
        if ($weather) {
            $weatherBlock = sprintf(
                '<div style="background:#fff3cd;padding:12px;border-radius:8px;margin:16px 0;"><strong>🌤 Prévision météo :</strong> %s</div>',
                htmlspecialchars($weather, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );
        }

        $days = max(1, (int) $reservation->getStartDate()->diff($reservation->getEndDate())->days);

        $email = (new Email())
            ->from(self::SENDER)
            ->to($user->getEmail())
            ->subject(sprintf('Réservation confirmée — %s', $reservation->getEventCity()))
            ->html(sprintf(
                '<h1>Réservation confirmée ✅</h1>
                <p>Bonjour %s,</p>
                <p>Votre réservation à <strong>%s</strong> du <strong>%s</strong> au <strong>%s</strong> (%d jour%s) est confirmée.</p>
                <h2>Matériel réservé :</h2><ul>%s</ul>
                <p><strong>Total : %s €</strong></p>
                %s
                <p>Merci de votre confiance !</p>',
                htmlspecialchars($user->getFirstName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($reservation->getEventCity(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $reservation->getStartDate()->format('d/m/Y'),
                $reservation->getEndDate()->format('d/m/Y'),
                $days,
                $days > 1 ? 's' : '',
                $lines,
                htmlspecialchars((string) $reservation->getTotalAmount(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $weatherBlock
            ));

        $this->mailer->send($email);
    }

    public function sendMaintenanceAssignment(Maintenance $maintenance): void
    {
        $technician = $maintenance->getTechnician();
        $equipment = $maintenance->getEquipment();

        $email = (new Email())
            ->from(self::SENDER)
            ->to($technician->getEmail())
            ->subject(sprintf('Nouvelle intervention assignée — %s', $equipment->getName()))
            ->html(sprintf(
                '<h1>Intervention assignée 🔧</h1>
                <p>Bonjour %s,</p>
                <p>Une nouvelle intervention vous a été assignée :</p>
                <ul>
                    <li><strong>Équipement :</strong> %s</li>
                    <li><strong>Type :</strong> %s</li>
                    <li><strong>Date :</strong> %s</li>
                    <li><strong>Description :</strong> %s</li>
                </ul>
                <p>Merci de vous en occuper dans les meilleurs délais.</p>',
                htmlspecialchars($technician->getFirstName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($equipment->getName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($maintenance->getInterventionType(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $maintenance->getInterventionDate()->format('d/m/Y'),
                htmlspecialchars($maintenance->getDescription(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            ));

        $this->mailer->send($email);
    }

    public function sendQuoteStatusUpdate(Quote $quote): void
    {
        $user = $quote->getUser();
        $statusLabel = $quote->getStatus() === 'approved' ? 'approuvé ✅' : 'refusé ❌';
        $quotesUrl = $this->urlGenerator->generate('quote_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from(self::SENDER)
            ->to($user->getEmail())
            ->subject(sprintf('Votre devis a été %s', $statusLabel))
            ->html(sprintf(
                '<h1>Devis %s</h1>
                <p>Bonjour %s,</p>
                <p>Votre devis pour <strong>%s</strong> (%s – %s) a été <strong>%s</strong>.</p>
                <p>Montant estimé : <strong>%s €</strong></p>
                <p>Consultez vos devis depuis votre <a href="%s">espace client</a>.</p>',
                $statusLabel,
                htmlspecialchars($user->getFirstName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($quote->getEventCity() ?: 'votre événement', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $quote->getRequestedStartDate()->format('d/m/Y'),
                $quote->getRequestedEndDate()->format('d/m/Y'),
                $statusLabel,
                htmlspecialchars((string) $quote->getEstimatedAmount(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($quotesUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            ));

        $this->mailer->send($email);
    }
}
