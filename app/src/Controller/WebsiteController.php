<?php

namespace App\Controller;

use App\Form\WebsiteFormType;
use App\Service\IoTAgentService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

class WebsiteController extends AbstractController
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly Security $security
    ) {
    }

    #[Route('/website/create', name: 'app_website_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(WebsiteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $devicesCounter = 1 + (int) json_decode($this->ioTAgentService->getServices($this->security->getUser()->getUserIdentifier()), true)['count'];

            $username = $this->security->getUser()->getUserIdentifier();

            $devices = [
                'devices' => [
                    [
                        'device_id' => 'sensor' . $devicesCounter,
                        'entity_name' => 'urn:ngsi-ld:Sensor:Sensor' . $devicesCounter,
                        'entity_type' => 'Sensor',
                        'timezone' => 'Europe/Madrid',
                        'attributes' => [
                            [
                                'object_id' => 's',
                                'name' => 'status',
                                'type' => 'Integer'
                            ]
                        ],
                        'static_attributes' => [
                            [
                                'name' => 'name',
                                'type' => 'Text',
                                'value' => $data['name']
                            ],
                            [
                                'name' => 'description',
                                'type' => 'Text',
                                'value' => $data['description']
                            ],
                            [
                                'name' => 'url',
                                'type' => 'Url',
                                'value' => $data['url']
                            ],
                            [
                                'name' => 'organization',
                                'type' => 'Text',
                                'value' => $data['organization']
                            ],
                            [
                                'name' => 'refDevice',
                                'type' => 'Relationship',
                                'value' => 'urn:ngsi-ld:Sensor:Sensor' . $devicesCounter
                            ],
                            [
                                'name' => 'refWebSite',
                                'type' => 'Relationship',
                                'value' => 'urn:ngsi-ld:WebSite:' . preg_replace('/[^a-zA-Z0-9]/', '', $data['name'])
                            ],
                        ],
                    ]
                ]
            ];

            $response = $this->ioTAgentService->createDevice($devices, $username);

            if ($response->getStatusCode() == '200') {
                $this->addFlash(
                    'success',
                    "The organization has been successfully created"
                );
            } else {
                $message = $response->getContent();
                $this->addFlash(
                    'error',
                    "An error was detected during the process: ($message)"
                );
            }
        }

        return $this->redirectToRoute('app_main_index');
    }

    #[Route('/website/delete', name: 'app_website_delete')]
    public function delete(#[MapQueryParameter()] string $id): Response
    {
        $currentUser = $this->security->getUser()->getUserIdentifier();
        $this->ioTAgentService->deleteDevice($id,$currentUser);
        return $this->redirectToRoute('app_main_index');
    }
}
