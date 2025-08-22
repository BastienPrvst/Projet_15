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
}
