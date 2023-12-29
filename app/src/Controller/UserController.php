<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager
    ){}

    #[Route('/user/update', name: 'app_user_update')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->security->getUser();
            $user->setEmail((string) $data->getEmail());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                "The email has been successfully updated"
            );
        }
        return $this->redirectToRoute('app_settings_index');
    }
}
