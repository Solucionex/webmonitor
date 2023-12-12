<?php

namespace App\Controller;

use App\Form\ServiceFormType;
use App\Service\IoTAgentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceController extends AbstractController
{
    public function __construct(
        private IoTAgentService $ioTAgentService,
        private UuidFactory $uuidFactory,
    )
    {
    }

    #[Route('/fiware/servicio/nuevo', name: 'fiware_service_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(ServiceFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $uuid = $this->uuidFactory->create();
            $apikey = $uuid->toBase32();

            $services = [
                'services' => [
                    [
                        'apikey' => $apikey,
                        'cbroker' => 'http://orion:1026',
                        'entity_type' => 'Group',
                        'resource' => '/iot/d'
                    ]
                ]
            ];

            $response = $this->ioTAgentService->createService($services);

            $service_name = $data['group_name'];
            if ($response->getStatusCode() == '200') {
                $this->addFlash(
                    'success',
                    "El host <strong>$service_name</strong> se ha creado correctamente"
                );
            } else {
                $message = $response->getContent();
                $this->addFlash(
                    'error',
                    "Se ha detectado un error al eliminar el host <strong>$service_name</strong>: ($message)"
                );
            }
        }
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/fiware/service/eliminar/{apikey}', name: 'fiware_service_delete')]
    public function delete(Request $request, string $apikey): Response
    {
        $response = $this->ioTAgentService->deleteService($apikey);

        if ($response->getStatusCode() == '200') {
            $this->addFlash(
                'success',
                "El servicio se ha eliminado correctamente"
            );
        } else {
            $message = $response->getContent();
            $this->addFlash(
                'error',
                "Se ha detectado un error al eliminar el servicio: ($message)"
            );
        }

        return $this->redirectToRoute('app_dashboard');
    }
}
