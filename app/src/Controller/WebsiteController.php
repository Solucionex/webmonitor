<?php

namespace App\Controller;

use App\Form\WebsiteFormType;
use App\Service\IoTAgentService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\String\Slugger\SluggerInterface;

class WebsiteController extends AbstractController
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly Security $security,
        private readonly SluggerInterface $slugger,
        private readonly KernelInterface $kernel
    ) {
    }

    #[Route('/website/create', name: 'app_website_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(WebsiteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $counter = (int) json_decode($this->ioTAgentService->getDevices($this->security->getUser()->getUserIdentifier(), '/'.strtolower($data['organization'])), true)['count'];

            $username = $this->security->getUser()->getUserIdentifier();

            $newDeviceCounter = $counter +1;

            $devices = [
                'devices' => [
                    [
                        'device_id' => str_replace('-','_',strtolower($this->slugger->slug($data['name']))),
                        'entity_name' => 'urn:ngsi-ld:Website:' . $data['name'],
                        'entity_type' => 'Website',
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
                                'value' => 'urn:ngsi-ld:Website:' . $data['name']
                            ],
                        ],
                    ]
                ]
            ];

            $response = $this->ioTAgentService->createDevice($devices, $username, '/'.strtolower($data['organization']));

            if ($response->getStatusCode() == '200') {
                $this->addFlash(
                    'success',
                    "The website has been successfully created"
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
    public function delete(
        #[MapQueryParameter()] string $id,
        #[MapQueryParameter()] string $service,
        #[MapQueryParameter()] string $service_path,
    ): Response
    {
        $currentUser = $this->security->getUser()->getUserIdentifier();
        $this->ioTAgentService->deleteDevice($id, $service, $service_path);
        return $this->redirectToRoute('app_main_index');
    }

    #[Route('/website/update', name: 'app_website_update')]
    public function update(): Response
    {
        $currentUser = $this->security->getUser()->getUserIdentifier();

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:devices:update',
            'username' => $currentUser
        ]);

        $application->run($input);

        $this->addFlash(
            'success',
            "The websites statuses has been successfully updated"
        );

        return $this->redirectToRoute('app_main_index');
    }
}
