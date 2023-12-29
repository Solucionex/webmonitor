<?php

namespace App\Controller;

use App\Form\WebsiteFormType;
use App\Service\ContextBrokerService;
use App\Service\CygnusDatabaseService;
use App\Service\IoTAgentService;
use App\Form\OrganizationFormType;
use DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly ContextBrokerService $contextBrokerService,
        private readonly Security $security,
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly CygnusDatabaseService $cygnusDatabaseService
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
        $response = json_decode($this->ioTAgentService->getServices($currentUser, '/*'), true);
        if($response['services']){
            $organizations = $response['services'];
        }

        $websites = [];
        foreach ($organizations as $organization){
            $response = json_decode($this->ioTAgentService->getDevices($currentUser, $organization['subservice']), true);
            if($response['devices']){
                $websites = array_merge($websites,$response['devices']);
            }
        }

        foreach ($websites as $key => $website){
            $response = json_decode($this->contextBrokerService->getEntity($website['entity_name'], $this->security->getUser()->getUserIdentifier()), true);
            $websites[$key]['current_status'] = isset($response['status']['value']) ? $response['status']['value'] : 0;
        }

        $data = $this->cygnusDatabaseService->getData();

        if($data){
            $historyChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

            $labels = [];
            $sites = [];

            foreach ($data as $item){
                $labels[] = date('d/M H:i', $item['recvTimeTs']);
                $sites[str_replace('urn:ngsi-ld:Website:', '', $item['entityId'])][] = $item['attrValue'];
            }

            $labels = array_values(array_unique($labels));
            $datasets = [];

            foreach ($sites as $site => $statuses)
            {
                $datasets[] = [
                    'label' => $site,
                    'data' => $statuses,
                    'fill' => true,
                ];
            }

            $historyChart->setData([
                'labels' => $labels,
                'datasets' => $datasets,
            ]);

            $historyChart->setOptions([
                'scales' => [
                    'type' => 'time',
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1,
                            'max' => 1,
                            'min' => 0,
                        ]
                    ]
                ]
            ]);
        }else{
            $historyChart = null;
        }

        return $this->render('main/index.html.twig', [
            'organizationForm' => $organizationForm,
            'websiteForm' => $websiteForm,
            'organizations' => $organizations,
            'websites' => $websites,
            'historyChart' => $historyChart
        ]);
    }
}
