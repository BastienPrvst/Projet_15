<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ProjectFixtures extends Fixture
{
	public function __construct(
		private readonly UserPasswordHasherInterface $passwordHasher)
	{

	}
    public function load(ObjectManager $manager): void
    {
		$faker = Faker::create();
	    $users = [];
	    $albums = [];
		for ($i = 0; $i < 10; $i++) {
			$album = new Album();
			$album->setName($faker->sentence(3));
			$albums[] = $album;
			$manager->persist($album);
		}

        for ($i = 0; $i < 50; $i++) {
			$user = new User();
	        $plainPassword = $faker->password();
			$user
				->setName($faker->name())
				->setEmail($faker->email())
				->setDescription($faker->text(100))
				->setPassword($this->passwordHasher->hashPassword($user, $plainPassword))
			;
			$users[] = $user;
			$manager->persist($user);
        }

		$maxUsers = count($users);
		$maxAlbums = count($albums);

		for ($i = 0; $i < 50; $i++) {
			$randUser = random_int(0, $maxUsers -1);
			$randAlbums = random_int(0, $maxAlbums - 1);
			$media = new Media();
			$media->setTitle($faker->sentence(3));
			$media->setUser($users[$randUser]);
			$media->setAlbum($albums[$randAlbums]);
			$media->setPath(sprintf('uploads/%03d.jpg', $i));

			$manager->persist($media);
		}

		$userAdmin = new User();
		$userAdmin
			->setName('Admin')
			->setEmail('admin@mail.com')
			->setPassword($this->passwordHasher->hashPassword($userAdmin, 'password'))
			->setRoles(["ROLE_ADMIN"]);
		$manager->persist($userAdmin);

        $manager->flush();
    }
}
