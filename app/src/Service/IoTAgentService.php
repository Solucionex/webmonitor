<?php

namespace App\Service;

use App\Service\NetworkService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class IoTAgentService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        #[Autowire(env: 'IOTA_NORTH_URL')]
        private readonly string $north_url,
        #[Autowire(env: 'IOTA_SOUTH_URL')]
        private readonly string $south_url,
    ) {
    }

    public function getInfo()
    {
        return $this->client->request('GET', $this->north_url . '/iot/about', [
            'headers' => [
                'Content-Type' => 'application/json',
                'fiware-service' => 'openiot',
                'fiware-servicepath' => '/'
            ]
        ])->getContent();
    }

    public function getDevices(string $fiwareService="openiot", string $fiwareServicePath="/")
    {
        return $this->client->request('GET', $this->north_url . '/iot/devices', [
            'headers' => [
                'Content-Type' => 'application/json',
                'fiware-service' => $fiwareService,
                'fiware-servicepath' => $fiwareServicePath
            ]
        ])->getContent();
    }

    public function getServices($fiwareService="openiot", $fiwareServicePath="/")
    {
        return $this->client->request('GET', $this->north_url . '/iot/services', [
            'headers' => [
                'Content-Type' => 'application/json',
                'fiware-service' => $fiwareService,
                'fiware-servicepath' => $fiwareServicePath
            ]
        ])->getContent();
    }

    public function createDevice(array $devices, string $fiwareService="openiot", string $fiwareServicePath="/"): Response
    {
        try {
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'fiware-service' => $fiwareService,
                    'fiware-servicepath' => $fiwareServicePath
                ],
                'json' => $devices
            ];
            $response = $this->client->request('POST', $this->north_url . '/iot/devices', $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function updateDevice(
        string $apikey,
        string $id,
        int $value
    ): Response
    {

        try {
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'query' => [
                    "k" => $apikey,
                    "i" => $id
                ],
                'json' => [ 's' => $value]
            ];
            $response = $this->client->request('POST', $this->south_url . '/iot/json', $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function deleteDevice(string $device_id, string $fiwareService="openiot", string $fiwareServicePath="/"): Response
    {
        try {
            $request = [
                'headers' => [
                    'fiware-service' => $fiwareService,
                    'fiware-servicepath' => $fiwareServicePath
                ]
            ];
            $response = $this->client->request('DELETE', $this->north_url . '/iot/devices/' . $device_id, $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function createService(array $services, string $fiwareService="openiot", string $fiwareServicePath="/")
    {
        try {
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'fiware-service' => $fiwareService,
                    'fiware-servicepath' => $fiwareServicePath
                ],
                'json' => $services
            ];
            $response = $this->client->request('POST', $this->north_url . '/iot/services', $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }

    public function deleteService(string $resource, string $apikey, string $fiwareService="openiot", string $fiwareServicePath="/"): Response
    {
        try {
            $request = [
                'headers' => [
                    'fiware-service' => $fiwareService,
                    'fiware-servicepath' => $fiwareServicePath
                ]
            ];
            $response = $this->client->request('DELETE', $this->north_url . '/iot/services/?resource='.$resource.'&apikey='.$apikey, $request);
            return new Response($response->getContent());
        } catch (\Throwable $th) {
            return new Response($th->getMessage(), $th->getCode());
        }
    }
}
