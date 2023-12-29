<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ContextBrokerService
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'ORION_URL')]
        private string $url,
    )
    {}

    public function getVersion()
    {
        return $this->client->request('GET', $this->url . '/version')->getContent();
    }

    public function getEntity(string $entity_name, string $service='openiot')
    {
        $request = [
            'headers' => [
                'fiware-service' => $service
            ]
        ];
        $response = $this->client->request('GET', $this->url . '/v2/entities/' . $entity_name, $request);

        if($response->getStatusCode() == 200){
            return $response->getContent();
        }else{
            return new Response('',Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function getEntities(string $service='openiot')
    {
        $request = [
            'headers' => [
                'fiware-service' => $service
            ]
        ];

        $response = $this->client->request('GET', $this->url . '/v2/entities', $request);

        if($response->getStatusCode() == 200){
            return $response->getContent();
        }else{
            return new Response('',Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function deleteEntity(string $entityName, string $service='openiot', string $servicePath='/')
    {
        try {
            $request = [
                'headers' => [
                    'fiware-service' => $service,
                    'fiware-servicepath' => $servicePath
                ]
            ];
            $response = $this->client->request('DELETE', $this->url . '/v2/entities/'.$entityName, $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function getSubscriptions(string $service='openiot', string $servicePath='/')
    {
        $request = [
            'headers' => [
                'fiware-service' => $service,
                'fiware-servicepath' => $servicePath
            ]
        ];

        return $this->client->request('GET', $this->url . '/v2/subscriptions', $request);
    }

    public function createSubscription(
        string $description,
        string $url,
        string $service='openiot',
        string|null $servicePath='/'
    )
    {
        $request = [
            'headers' => [
                'fiware-service' => $service,
                'fiware-servicepath' => $servicePath
            ],
            'query' => [],
            'json' => [
                'description' => $description,
                'subject' => [
                    'entities' => [
                        [
                            "idPattern" => ".*"
                        ]
                    ]
                ],
                'notification' => [
                    'http' => [
                        'url' => $url
                    ]
                ],
                `throttling` => 5

            ]
        ];

        return $this->client->request('POST', $this->url . '/v2/subscriptions', $request);
    }

    public function deleteSubscription(
        string $id,
        string $service='openiot',
    )
    {
        $request = [
            'headers' => [
                'fiware-service' => $service,
            ]
        ];

        return $this->client->request('DELETE', $this->url . '/v2/subscriptions/'.$id, $request);
    }
}
