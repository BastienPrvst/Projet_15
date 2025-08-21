<?php

namespace App\Tests\Functionnal\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ManageUserTest extends WebTestCase
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

	public function testFindGuest(): void
	{
		$numberOfGuests = count($this->userRepo->findGuest());
		$allUser = $this->userRepo->findAll();
		$realGuests = array_filter($allUser, static function ($user) {
			return $user->getRoles() !== "ROLE_ADMIN";
		});
		self::assertSame(count($realGuests), $numberOfGuests);
	}

	public function testCreateUser(): void
	{
		$user = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($user);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('admin_user_add'));
		self::assertResponseIsSuccessful();
		$form = $crawler->selectButton('Ajouter')->form();
		$form['user[name]'] = 'test';
		$form['user[email]'] = 'test@test.com';
		$form['user[password][first]'] = 'TestTest123!';
		$form['user[password][second]'] = 'TestTest123!';
		$form['user[description]'] = 'blabla';
		$this->client->submit($form);

		$url = $this->urlGenerator->generate('admin_user_index', ['pageUser' => 1]);
		self::assertResponseRedirects($url);
		$user = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		self::assertNotNull($user);
	}
}
