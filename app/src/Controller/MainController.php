<?php

namespace App\Controller;

use App\Form\WebsiteFormType;
use App\Service\IoTAgentService;
use App\Form\OrganizationFormType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly Security $security,
    )
    {
        
    }
    #[Route('/', name: 'app_main_index')]
    public function index(): Response
    {
        $organizationForm = $this->createForm(OrganizationFormType::class, null, [
            'action' => $this->generateUrl('app_organization_create'),
        ]);
        $websiteForm = $this->createForm(WebsiteFormType::class, null, [
            'action' => $this->generateUrl('app_website_create'),
        ]);

        $currentUser = $this->security->getUser()->getUserIdentifier();

        $organizations = [];
        $response = json_decode($this->ioTAgentService->getServices($currentUser), true);
        if($response['services']){
            $organizations = $response['services'];
        }

        $websites = [];
        $response = json_decode($this->ioTAgentService->getDevices($currentUser), true);
        if($response['devices']){
            $websites = $response['devices'];
        }

        $alerts = [];
        
        return $this->render('main/index.html.twig', [
            'organizationForm' => $organizationForm,
            'websiteForm' => $websiteForm,
            'organizations' => $organizations,
            'websites' => $websites,
            'alerts' => $alerts
        ]);
    }
}
