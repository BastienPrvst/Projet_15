<?php

namespace App\Tests\Unit;

use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserBlocking(): void
    {
		//Test non bloqué
        $user = new User();
		$user->setBlocked(false);
		$this->assertFalse($user->isBlocked());
	    $this->assertNotContains("ROLE_BLOCKED", $user->getRoles());

		//Test bloqué
		$user->setBlocked(true);
		$this->assertTrue($user->isBlocked());
	    $this->assertContains("ROLE_BLOCKED", $user->getRoles());
    }

	public function testUserMedias(): void
	{
		$user = new User();
		$media = new Media();
		//Ajout media
		$user->addMedia($media);
		$this->assertContains($media, $user->getMedias());

		//Suppression media
		$user->removeMedia($media);
		$this->assertNotContains($media, $user->getMedias());
	}
}
