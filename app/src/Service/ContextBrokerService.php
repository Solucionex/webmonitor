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

    public function getEntities()
    {
        return $this->client->request('GET', $this->url . '/v2/entities',)->getContent();
    }

    public function createEntity(array $data)
    {
        try {
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'fiware-service' => 'openiot',
                    'fiware-servicepath' => '/'
                ],
                'json' => $data
            ];
            $response = $this->client->request('POST', $this->url . '/v2/entities', $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function deleteEntity(string $id)
    {
        return new Response();
    }
}
