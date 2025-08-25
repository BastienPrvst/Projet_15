<?php

namespace App\Tests\Functionnal\Auth;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

	private $client;
	private $urlGenerator;
	private $userRepo;
	private $entityManager;
	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->urlGenerator = $this->client->getContainer()->get('router.default');
		$this->userRepo = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
		$this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
	}

    public function testUserIsBlockedInLogin(): void
    {
        $user = $this->userRepo->findOneBy(["email" => "admin@mail.com"]);
		$user->setBlocked(true);
		$this->entityManager->persist($user);
		$this->entityManager->flush();

		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_login'));
		$form = $crawler->selectButton('Connexion')->form();
		$form['_username'] = 'admin@mail.com';
		$form['_password'] = 'password';
		$this->client->submit($form);
	    $this->client->followRedirect();
	    self::assertSelectorTextContains('div.alert-danger', 'Votre compte est bloqué.');
    }
}
