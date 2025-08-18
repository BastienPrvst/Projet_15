<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
	)
	{
	}

	#[Route('/admin/user', name: 'admin_user_index')]
    public function index(Request $request): Response
    {
	    $userRepo = $this->entityManager->getRepository(User::class);
	    $page = $request->query->getInt('pageUser', 1);

	    $criteria = [];

	    if (!$this->isGranted('ROLE_ADMIN')) {
		    $criteria['user'] = $this->getUser();
	    }

	    $users = $userRepo->findBy(
		    $criteria,
		    ['id' => 'ASC'],
		    25,
		    25 * ($page - 1)
	    );
	    $total = $userRepo->count();

	    return $this->render('admin/user/index.html.twig', [
		    'users' => $users,
		    'total' => $total,
		    'pageUser' => $page
	    ]);
    }

	#[Route(path: '/admin/user/add', name: 'admin_user_add')]
	public function add(Request $request, UserPasswordHasherInterface $passwordHasher): Response
	{
		$user = new User();

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
			$user->setPassword($hashedPassword);
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $this->redirectToRoute('admin_user_index');
		}

		return $this->render('admin/user/add.html.twig', [
			'form' => $form
		]);
	}

	#[Route(path: '/admin/user/modify/{id}', name: 'admin_user_modify')]
	public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
	{
		$form = $this->createForm(UserType::class, $user, [
			'type' => 'edit'
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
			$user->setPassword($hashedPassword);
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $this->redirectToRoute('admin_user_index');
		}
		return $this->render('admin/user/edit.html.twig', [
			'user' => $user,
			'form' => $form
		]);

	}


	#[Route(path: '/admin/user/delete/{id}', name: 'admin_user_delete')]
	public function delete(int $id): RedirectResponse
	{
		$user = $this->entityManager->getRepository(User::class)->find($id);

		if ($user){
			$this->entityManager->remove($user);
			$this->entityManager->flush();
		}

		return $this->redirectToRoute('admin_user_index');
	}
}
