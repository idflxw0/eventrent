<?php

namespace App\Twig;

use App\Service\MercureJwtProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
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

    public function getSubscriberToken(): ?string
    {
        $user = $this->security->getUser();
        if (!$user || !method_exists($user, 'getId')) {
            return null;
        }

        return $this->jwtProvider->createSubscriberToken($user->getId());
    }
}
