<?php

namespace App\Twig;

use App\Entity\Equipment;
use App\Service\MercureJwtProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MercureExtension extends AbstractExtension
{
    public function __construct(
        private readonly MercureJwtProvider $jwtProvider,
        private readonly Security $security,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mercure_subscriber_token', $this->getSubscriberToken(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('status_label', $this->statusLabel(...)),
            new TwigFilter('price_eur', $this->priceEur(...)),
        ];
    }

    public function getSubscriberToken(): ?string
    {
        $user = $this->security->getUser();
        if (!$user || !method_exists($user, 'getId')) {
            return null;
        }

        return $this->jwtProvider->createSubscriberToken($user->getId());
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            Equipment::STATUS_AVAILABLE        => 'Disponible',
            Equipment::STATUS_MAINTENANCE      => 'En maintenance',
            Equipment::STATUS_OUT_OF_SERVICE   => 'Hors service',
            'pending'                          => 'En attente',
            'confirmed'                        => 'Confirmée',
            'completed'                        => 'Terminée',
            'cancelled'                        => 'Annulée',
            'approved'                         => 'Approuvé',
            'refused'                          => 'Refusé',
            'rejected'                         => 'Refusé',
            'expired'                          => 'Expiré',
            default                            => ucfirst($status),
        };
    }

    public function priceEur(float|string|null $amount): string
    {
        if ($amount === null) {
            return '—';
        }

        return number_format((float) $amount, 2, ',', ' ') . ' €';
    }
}
