<?php

namespace App\Security\Voter;

use App\Entity\Reservation;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReservationVoter extends Voter
{
    public const CANCEL = 'RESERVATION_CANCEL';
    private const ALLOWED_STATUSES = ['pending', 'confirmed'];

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CANCEL && $subject instanceof Reservation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user) {
            return false;
        }

        /** @var Reservation $reservation */
        $reservation = $subject;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($reservation->getUser() !== $user) {
            return false;
        }

        if (!in_array($reservation->getStatus(), self::ALLOWED_STATUSES, true)) {
            return false;
        }

        $now = new \DateTimeImmutable();
        $deadline = $reservation->getStartDate()->modify('-48 hours');

        return $now < $deadline;
    }
}
