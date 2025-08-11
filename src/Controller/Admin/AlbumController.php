<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AlbumController extends AbstractController
{

	#[Route(path: '/admin/album', name: 'admin_album_index')]
	public function index(): Response
	{
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }


	#[Route(path: '/admin/album/add', name: 'admin_album_add')]
	public function add(Request $request): RedirectResponse|Response
	{
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($album);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }


	#[Route(path: '/admin/album/update/{id}', name: 'admin_album_update')]
	public function update(Request $request, int $id): RedirectResponse|Response
	{
        $album = $this->getDoctrine()->getRepository(Album::class)->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }


	#[Route(path: '/admin/album/delete/{id}', name: 'admin_album_delete')]
	public function delete(int $id): RedirectResponse
	{
        $media = $this->getDoctrine()->getRepository(Album::class)->find($id);
        $this->getDoctrine()->getManager()->remove($media);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}