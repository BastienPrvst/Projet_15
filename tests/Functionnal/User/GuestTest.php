<?php

namespace App\Tests\Functionnal\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GuestTest extends WebTestCase
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
}
