<?php

namespace App\Tests\Functionnal\Front;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontTest extends WebTestCase
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

	public function testHome(): void
	{
		$this->client->request('GET', '/');
		self::assertSelectorTextContains('h2', 'Photographe');
	}

	public function testGuestsPage(): void
	{
		$crawler = $this->client->request('GET', '/guests');
		$allUser = $this->entityManager->getRepository(User::class)->findAll();

		self::assertResponseIsSuccessful();
		self::assertSelectorTextContains('h3', 'Invités');
		self::assertSame(count($allUser), $crawler->filter('div.guest')->count());

	}

	public function testGuestsShow(): void
	{
		$user = $this->userRepo->findOneBy([]);
		$allUserMedias = $user->getMedias();
		$crawler = $this->client->request('GET', $this->urlGenerator->generate('guest', ['id' => $user->getId()]));
		self::assertSame(count($allUserMedias), $crawler->filter('div.media')->count());
	}

	public function testPortfolioForAnAlbum(): void
	{
		$albums = $this->entityManager->getRepository(Album::class)->findAll();
		$totalAlbums = count($albums);
		$mediaRepo = $this->entityManager->getRepository(Media::class);
		$rand = random_int(0, $totalAlbums -1);
		$album = $albums[$rand];
		$id = $album->getId();
		$expectedMedias = $mediaRepo->findBy(['album' => $album]);

		$crawler = $this->client->request('GET', $this->urlGenerator->generate('portfolio', ['id' => $id]));
		self::assertSame(count($expectedMedias), $crawler->filter('div.media')->count());
	}

	public function testPortfolioAll():void
	{
		$user = $this->entityManager->getRepository(User::class)
			->findOneBy(['email' => 'admin@mail.com']);
		$totalMedias = count($this->entityManager->getRepository(Media::class)
			->findBy(['user' => $user]));
		$crawler = $this->client->request('GET', '/portfolio');
		self::assertSame($totalMedias, $crawler->filter('div.media')->count());
	}

	public function testAbout(): void
	{
		$this->client->request('GET', '/about');
		self::assertSelectorTextContains('h2', 'Qui suis-je ?');
	}

}
