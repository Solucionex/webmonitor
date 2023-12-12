<?php

namespace App\Controller;

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

    #[Route('/docker', name: 'app_docker')]
    public function docker(): Response
    {
        $containers = [
            'web app' => $this->networkService->ping('app','80')->getStatusCode() == 200 ? true : false,
            'iot agent' => $this->networkService->ping('iot-agent','7896')->getStatusCode() == 200 ? true : false,
            'context broker' => $this->networkService->ping('orion','1026')->getStatusCode() == 200 ? true : false,
            'mongodb' => $this->networkService->ping('mongodb','27017')->getStatusCode() == 200 ? true : false,
            'cygnus' => $this->networkService->ping('cygnus','5080')->getStatusCode() == 200 ? true : false,
            'mariadb' => $this->networkService->ping('mariadb','3306')->getStatusCode() == 200 ? true : false,
            'phpmyadmin' => $this->networkService->ping('phpmyadmin','80')->getStatusCode() == 200 ? true : false,
            'grafana' => $this->networkService->ping('grafana','3000')->getStatusCode() == 200 ? true : false,
            'sensores' => $this->networkService->ping('sensors','5000')->getStatusCode() == 200 ? true : false,
        ];

        return $this->render('settings/index.html.twig', [
            'containers' => $containers,
        ]);
    }

    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', []);
    }
}
