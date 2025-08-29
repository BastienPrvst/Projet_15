<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MediaController extends AbstractController
{

	public function __construct(
		private readonly EntityManagerInterface $entityManager,
	)
	{
	}

	#[Route(path: '/admin/media', name: 'admin_media_index')]
	public function index(Request $request): Response
	{
		$mediasRepo = $this->entityManager->getRepository(Media::class);
        $page = $request->query->getInt('mediaPage', 1);

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
	        $total = $mediasRepo->count([
		        'user' => $this->getUser(),
	        ]);
        }else{
			$total = $mediasRepo->count();
        }

        $medias = $mediasRepo->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );


        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'mediaPage' => $page
        ]);
    }

	#[Route(path: '/admin/media/add', name: 'admin_media_add')]
	public function add(Request $request, ParameterBagInterface $parameterBag): RedirectResponse|Response
	{
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }
			$fileName = md5(uniqid('', true)) . '.' . $media->getFile()?->guessExtension();
            $media->setPath('uploads/' . $fileName);
			$root = $parameterBag->get('kernel.project_dir');
            $media->getFile()?->move($root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' , $fileName);
            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }


	#[Route(path: '/admin/media/delete/{media}', name: 'admin_media_delete')]
	public function delete(Media $media): RedirectResponse
	{
		if (
			$media->getUser() !== $this->getUser() &&
			!in_array("ROLE_ADMIN", $this->getUser()?->getRoles(), true)
		) {
			return $this->redirectToRoute('home');
		}

		$this->entityManager->remove($media);
		$this->entityManager->flush();
		if (file_exists($media->getPath())) {
			unlink($media->getPath());
		}


        return $this->redirectToRoute('admin_media_index');
    }
}