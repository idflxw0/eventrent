<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $crawler = $client->getCrawler();

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@eventrent.com',
            '_password' => 'admin123',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $crawler = $client->getCrawler();

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'wrong@example.com',
            '_password' => 'wrong',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testCatalogPageIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/catalogue');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Catalogue');
    }

    public function testReservationRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reservations');

        $this->assertResponseRedirects('/login');
    }
}
