<?php

namespace App\Tests\Functionnal\Album;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ManageAlbumTest extends WebTestCase
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

	public function testCreateAlbum(): void
	{
		$adminUser = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($adminUser);

		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_album_add'));
		$form = $crawler->selectButton('Ajouter')->form();
		$form['album[name]'] = 'test';

		$this->client->submit($form);

		self::assertResponseRedirects();
		self::assertResponseStatusCodeSame(302);
		$this->client->followRedirect();
		$addedAlbum = $this->entityManager->getRepository(Album::class)->findOneBy(['name' => 'test']);
		self::assertNotNull($addedAlbum);
		$this->assertSame(
			'/admin/album',
			$this->client->getRequest()->getPathInfo()
		);

	}

	public function testUpdateAlbum(): void
	{
		$adminUser = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($adminUser);
		$albumToUpdate = $this->entityManager->getRepository(Album::class)->findOneBy([]);
		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_album_update', ['album' => $albumToUpdate->getId()]));
		$form = $crawler->selectButton('Modifier')->form();
		$form['album[name]'] = 'test';
		$this->client->submit($form);
		self::assertResponseRedirects();
		$this->client->followRedirects();

		$albumUpdated = $this->entityManager->getRepository(Album::class)->findOneBy(['name' => 'test']);

		$this->assertNotNull($albumUpdated);

	}

	public function testDeleteAlbum(): void
	{
		$adminUser = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($adminUser);

		$albumToDelete = $this->entityManager->getRepository(Album::class)->findOneBy([]);
		$id = $albumToDelete->getId();
		$this->client->request('GET', $this->urlGenerator->generate('admin_album_delete', ['album' => $id ]));
		self::assertResponseRedirects();

		$albumDeleted = $this->entityManager->getRepository(Album::class)->findOneBy(['id' => $albumToDelete->getId()]);
		self::assertNull($albumDeleted);
	}
}
