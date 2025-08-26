<?php

namespace App\Tests\Functionnal\Media;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Faker\Core\File;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ManageMediaTest extends WebTestCase
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

	public function testShowMediasAsAdmin(): void
	{
		$userAdmin = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($userAdmin);
		$allMedias = $this->entityManager->getRepository(Media::class)->findAll();
		$totalMedias = count($allMedias);
		$expectedMaxPages = (int) ceil($totalMedias / 25);
		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_media_index'));
		$link = $crawler->filterXPath('//a[contains(text(), "Dernière page")]')->link();
		$uri = $link->getUri();
		$uri = explode('=', $uri);
		self::assertSame($expectedMaxPages, (int) $uri[1]);
	}

	public function testShowMediasAsUser(): void
	{
		$allGuests = $this->userRepo->findGuest();
		$rand = random_int(0, count($allGuests) - 1);
		/* @var $user User */
		$guestData = $allGuests[$rand];
		$user = $this->userRepo->find($guestData['id']);
		$this->client->loginUser($user);
		$usermedia = $user->getMedias();
		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_media_index'));
		$expectedMaxPages = (int) ceil(count($usermedia) / 25);

		if ($expectedMaxPages === 0 || $expectedMaxPages === 1) {
			self::assertSame(
				(count($usermedia)),
				$crawler->filter('tbody tr')->count()
			);

		}else{

			$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_media_index', [
				"mediaPage" => $expectedMaxPages
			]));

			self::assertSame(count($usermedia), ((25 * ($expectedMaxPages - 1)) + $crawler->filter('tbody tr')->count()));
		}
	}

	public function testAddMediaAsAdmin(): void
	{
		$userAdmin = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($userAdmin);
		$crawler = $this->client->request('GET', $this->urlGenerator->generate('admin_media_add'));

		self::assertResponseIsSuccessful();

		$form = $crawler->selectButton('Ajouter')->form();
		$album = $this->entityManager->getRepository(Album::class)->findOneBy([], ['id' => 'ASC']);

		$uploadedFile = new UploadedFile(
			__DIR__ . '/../../../public/images/ina.png',
			'ina.png',
			'image/png',
			null,
			true
		);

		$form['media[user]'] = $userAdmin->getId();
		$form['media[album]'] = $album->getId();
		$form['media[title]'] = 'test';
		$form['media[file]']->upload($uploadedFile);

		$this->client->submit($form);

		self::assertResponseRedirects('/admin/media');

		$media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'test']);
		self::assertNotNull($media);
		self::assertContains($media, $userAdmin->getMedias());
	}

	public function testDeleteMedia():void
	{
		$userAdmin = $this->userRepo->findOneBy(['email' => 'admin@mail.com']);
		$this->client->loginUser($userAdmin);
		$media = $this->entityManager->getRepository(Media::class)->findOneBy([]);
		$id = $media->getId();
		$this->client->request('GET', $this->urlGenerator->generate('admin_media_delete', ['id' => $media->getId()]));
		$mediaDeleted = $this->entityManager->getRepository(Media::class)->findOneBy(['id' => $id]);
		self::assertNull($mediaDeleted);

	}
}
