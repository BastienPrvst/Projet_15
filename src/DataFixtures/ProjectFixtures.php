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

	    $userAdmin = new User();
	    $userAdmin
		    ->setName('Admin')
		    ->setEmail('admin@mail.com')
		    ->setPassword($this->passwordHasher->hashPassword($userAdmin, 'password'))
		    ->setRoles(["ROLE_ADMIN"]);
		$users[] = $userAdmin;
	    $manager->persist($userAdmin);

	    $maxUsers = count($users);
	    $maxAlbums = count($albums);

	    for ($i = 0; $i < 500; $i++) {

			$randUser = random_int(0, $maxUsers -1);
			$randAlbums = random_int(0, $maxAlbums - 1);
			$media = new Media();
			$media->setTitle($faker->sentence(3));
			$media->setUser($users[$randUser]);
			$media->setAlbum($albums[$randAlbums]);
			$randMedia = random_int(1, 30);
			$media->setPath('uploads/fix-' . $randMedia . '.jpg');

			$manager->persist($media);
		}

        $manager->flush();
    }
}
