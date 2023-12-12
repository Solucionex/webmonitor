<?php

namespace App\Controller;

use App\Form\DeviceFormType;
use App\Service\IoTAgentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeviceController extends AbstractController
{
    public function __construct(
        private IoTAgentService $ioTAgentService,
    ) {
    }

    #[Route('/fiware/dispositivo/nuevo', name: 'fiware_device_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(DeviceFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $devices = [
                'devices' => [
                    [
                        'device_id' => $data['host_id'],
                        'entity_name' => 'urn:ngsi-ld:Host:001',
                        'entity_type' => $data['host_name'],
                        'timezone' => 'Europe/Madrid',
                        'attributes' => [
                            [
                                'object_id' => 's',
                                'name' => 'status',
                                'type' => 'Boolean'
                            ]
                        ],
                    ]
                ]
            ];

            $response = $this->ioTAgentService->createDevice($devices);

            $device_id = $data['device_id'];
            if ($response->getStatusCode() == '200') {
                $this->addFlash(
                    'success',
                    "El dispositivo <strong>$device_id</strong> se ha creado correctamente"
                );
            } else {
                $message = $response->getContent();
                $this->addFlash(
                    'error',
                    "Se ha detectado un error al eliminar el dispositivo <strong>$device_id</strong>: ($message)"
                );
            }

            return $this->redirectToRoute('app_fiware_index');
        }
    }

    #[Route('/fiware/dispositivo/eliminar/{id}', name: 'fiware_device_delete')]
    public function delete(Request $request, string $id): Response
    {
        $response = $this->ioTAgentService->deleteDevice($id);

        if ($response->getStatusCode() == '200') {
            $this->addFlash(
                'success',
                "El dispositivo <strong>$id</strong> se ha eliminado correctamente"
            );
        } else {
            $message = $response->getContent();
            $this->addFlash(
                'error',
                "Se ha detectado un error al eliminar el dispositivo <strong>$id</strong>: ($message)"
            );
        }

        return $this->redirectToRoute('app_fiware_index');
    }
}
