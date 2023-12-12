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

            $username = $this->security->getUser()->getUserIdentifier();
            $name = $data['name'];
            $description = $data['description'];
            $apikey = ($this->uuidFactory->create())->toBase32();

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

            $response = $this->ioTAgentService->createService($services, $username);

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

    #[Route('/organization/delete', name: 'app_organization_delete')]
    public function delete(#[MapQueryParameter] string $r, #[MapQueryParameter] string $k): Response
    {
        $response = $this->ioTAgentService->deleteService($r, $k, $this->security->getUser()->getUserIdentifier());

        if ($response->getStatusCode() == '200') {
            $this->addFlash(
                'success',
                "The organization has been successfully deleted"
            );
        } else {
            $message = $response->getContent();
            $this->addFlash(
                'error',
                "An error was detected during the process: ($message)"
            );
        }
        return $this->redirectToRoute('app_main_index');
    }
}
