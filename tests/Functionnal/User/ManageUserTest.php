<?php

namespace App\Tests\Functionnal\User;

use App\Entity\User;
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
			return !in_array('"ROLE_ADMIN"', $user->getRoles(), true);
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

	public function testUpdateUser(): void
	{
		$user = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($user);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('admin_user_modify', [
			'user' => $user->getId()
		]));
		self::assertResponseIsSuccessful();
		$form = $crawler->selectButton('Modifier')->form();
		$form['user[name]'] = 'updated';
		$this->client->submit($form);
		self::assertResponseStatusCodeSame(302);
		$updatedUser = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		self::assertNotNull($updatedUser);
		self::assertEquals('updated', $updatedUser->getName());
	}

	public function testDeleteUser(): void
	{
		$user = new User();
		$user
			->setEmail('imSorryLittleOne@ouch.com')
			->setPassword('aaaaaaaaaa')
			->setName('Adios')
			->setDescription('brrrr')
		;
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		$id = $user->getId();
		self::assertCount(1, $this->userRepo->findBy(['id' => $id]));


		$userToLog = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($userToLog);
		$totalUser = $this->userRepo->count([]);
		$maxPage = ceil($totalUser / 25);
		$this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('admin_user_delete', [
			'user' => $id,
			'userPage' => $maxPage
		]));
		self::assertResponseStatusCodeSame(302);
		self::assertResponseRedirects($this->urlGenerator->generate('admin_user_index'));
		$userDeleted = $this->userRepo->findOneBy(['email' => 'imSorryLittleOne@ouch.com']);
		self::assertNull($userDeleted);
	}
}
