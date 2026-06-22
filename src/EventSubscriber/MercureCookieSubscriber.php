<?php

namespace App\EventSubscriber;

use App\Service\MercureJwtProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class MercureCookieSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MercureJwtProvider $jwtProvider,
    ) {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!method_exists($user, 'getId')) {
            return;
        }

        $token = $this->jwtProvider->createSubscriberToken($user->getId());

        $cookie = Cookie::create('mercure_token', $token)
            ->withHttpOnly(false)
            ->withSecure(false)
            ->withSameSite('lax')
            ->withPath('/');

        $event->getResponse()->headers->setCookie($cookie);
    }

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLoginSuccess'];
    }
}
