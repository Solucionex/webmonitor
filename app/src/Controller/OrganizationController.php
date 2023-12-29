<?php

namespace App\Controller;

use App\Service\IoTAgentService;
use App\Form\OrganizationFormType;
use App\Service\ContextBrokerService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrganizationController extends AbstractController
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly ContextBrokerService $contextBrokerService,
        private readonly UuidFactory $uuidFactory,
        private readonly Security $security,
    ) {
    }

    #[Route('/organization/create', name: 'app_organization_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(OrganizationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $name = $data['name'];
            $description = $data['description'];
            $apikey = ($this->uuidFactory->create())->toBase32();

            $service = $this->security->getUser()->getUserIdentifier();
            $servicePath = '/'.strtolower($name);

            $services = [
                'services' => [
                    [
                        'apikey' => $apikey,
                        'cbroker' => 'http://orion:1026',
                        'entity_type' => 'Organization',
                        'resource' => '/iot/json',
                        'timezone' => 'Europe/Madrid',
                        'static_attributes' => [
                            [
                                'name' => 'name',
                                'type' => 'Text',
                                'value' => $name
                            ],
                            [
                                'name' => 'description',
                                'type' => 'Text',
                                'value' => $description
                            ],
                            [
                                'name' => 'refOrganization',
                                'type' => 'Relationship',
                                'value' => 'urn:ngsi-ld:Organization:' . preg_replace('/[^a-zA-Z0-9]/', '', $name)
                            ],
                        ]
                    ]
                ]
            ];

            $response = $this->ioTAgentService->createService($services, $service, $servicePath);
            $cygnus = $this->contextBrokerService->createSubscription(
                "Cygnus subscription for $name",
                "http://cygnus:5050/notify",
                $service,
                $servicePath
            );
            $alert = $this->contextBrokerService->createSubscription(
                "Alerts subscription for $name",
                "http://app/endpoint/notification",
                $service,
                $servicePath
            );

            if ($response->getStatusCode() == '200' && $cygnus->getStatusCode() == '201' && $alert->getStatusCode() == '201') {
                $this->addFlash(
                    'success',
                    "The organization has been successfully created"
                );
            } else {
                $message = $response->getContent();
                $this->addFlash(
                    'error',
                    "An error was detected during the process"
                );
            }
        }


        return $this->redirectToRoute('app_main_index');
    }

    #[Route('/organization/delete', name: 'app_organization_delete')]
    public function delete(
        #[MapQueryParameter] string $resource,
        #[MapQueryParameter] string $key,
        #[MapQueryParameter] string $name
    ): Response
    {
        $service = $this->security->getUser()->getUserIdentifier();
        $servicePath = '/'.strtolower($name);

        // Delete all devices and entities related to the service (organization)
        $response = json_decode($this->ioTAgentService->getDevices($service, $servicePath), true);
        if($response['devices']){
            foreach ($response['devices'] as $device){
                $this->contextBrokerService->deleteEntity($device['entity_name'], $service, $servicePath);
                $this->ioTAgentService->deleteDevice($device['device_id'], $service, $servicePath);
            }
        }

        // Delete all subscriptions related to the service (organization)
        $response = $this->contextBrokerService->getSubscriptions($service,$servicePath);
        if(json_decode($response->getContent(),true)){
            foreach (json_decode($response->getContent(), true) as $subscription){
                $this->contextBrokerService->deleteSubscription($subscription['id'], $service, $servicePath);
            }
        }

        // Delete the service (organization) itself
        $response = $this->ioTAgentService->deleteService($resource, $key, $service, $servicePath);

        if ($response->getStatusCode() == '200') {
            $this->addFlash(
                'success',
                "The organization has been successfully deleted"
            );
        } else {
            $message = $response->getContent();
            $this->addFlash(
                'error',
                "An error was detected during the process"
            );
        }
        return $this->redirectToRoute('app_main_index');
    }
}
