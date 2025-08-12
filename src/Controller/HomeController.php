<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
	)
	{
	}

	#[Route(path: '/', name: 'home')]
	public function home(): Response
	{
        return $this->render('front/home.html.twig');
    }

	#[Route(path: '/guests', name: 'guests')]
	public function guests(): Response
	{
        $guests = $this->entityManager->getRepository(User::class)->findBy(['admin' => false]);
        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

	#[Route(path: '/guest/{id}', name: 'guest')]
	public function guest(int $id): Response
	{
        $guest = $this->entityManager->getRepository(User::class)->find($id);
        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route(path: '/portfolio/{id}', name: 'portfolio')]
	public function portfolio(?int $id = null): Response
    {
		$albumRepo = $this->entityManager->getRepository(Album::class);
		$mediaRepo = $this->entityManager->getRepository(Media::class);

	    $albums = $albumRepo->findAll();
	    $album = $id ? $albumRepo->find($id) : null;
	    $user = $this->entityManager->getRepository(User::class)->findOneBy(['admin' => true]);

	    $medias = $album
		    ? $mediaRepo->findBy(['album' => $album])
		    : $mediaRepo->findBy(['user' => $user]);
	    return $this->render('front/portfolio.html.twig', [
		    'albums' => $albums,
		    'album' => $album,
		    'medias' => $medias
	    ]);
    }

	#[Route(path: '/about', name: 'about')]
	public function about(): Response
	{
        return $this->render('front/about.html.twig');
    }
}