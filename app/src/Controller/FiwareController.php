<?php

namespace App\Controller;

use App\Form\DeviceFormType;
use App\Form\EntityFormType;
use App\Form\ServiceFormType;
use App\Service\IoTAgentService;
use App\Service\ContextBrokerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FiwareController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private IoTAgentService $ioTAgentService,
        private ContextBrokerService $contextBrokerService,
    ){}

    #[Route('/fiware', name: 'app_fiware_index')]
    public function index(): Response
    {
        $device_form = $this->createForm(DeviceFormType::class, null, [
            'action' => $this->generateUrl('fiware_device_create'),
        ]);

        $service_form = $this->createForm(ServiceFormType::class, null, [
            'action' => $this->generateUrl('fiware_service_create'),
        ]);

        $entity_form = $this->createForm(EntityFormType::class, null, [
            'action' => $this->generateUrl('fiware_entity_create'),
        ]);

        $devices = json_decode($this->ioTAgentService->getDevices())->devices;
        $services = json_decode($this->ioTAgentService->getServices())->services;
        $entities = json_decode($this->contextBrokerService->getEntities());

        return $this->render('fiware/index.html.twig', [
            'devices' => $devices,
            'services' => $services,
            'entities' => $entities,
            'device_form' => $device_form->createView(),
            'service_form' => $service_form->createView(),
            'entity_form' => $entity_form->createView(),
        ]);
    }
}
