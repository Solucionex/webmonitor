<?php

namespace App\Controller;

use App\Form\AlertFormType;
use App\Form\UserFormType;
use App\Service\NetworkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    public function __construct(
        private NetworkService $networkService,
    )
    {
    }

    #[Route('/settings', name: 'app_settings_index')]
    public function index(): Response
    {

        $userForm = $this->createForm(UserFormType::class, null, [
            'action' => $this->generateUrl('app_user_update'),
        ]);

        $alertForm = $this->createForm(AlertFormType::class, null, [
            'action' => $this->generateUrl('app_alert_update')
        ]);

        return $this->render('settings/index.html.twig', [
            'userForm' => $userForm,
            'alertForm' => $alertForm
        ]);
    }
}
