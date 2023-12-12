<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\IoTAgentService;
use App\Service\NetworkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:check-hosts',
    description: 'Checks all registered hosts urls',
)]
class CheckHostsCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private IoTAgentService $ioTAgentService,
        private NetworkService $networkService,
        private UserRepository $userRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $users = $this->userRepository->findAll();
        $endpoints = [];

        foreach ($users as $user) {
            $username = $user->getUserIdentifier();
            $response = $this->ioTAgentService->getDevices($username);
            $devices = json_decode($response,true)['devices'];
            array_push($endpoints,$devices);
        }

        foreach ($endpoints as $endpoint) {
            
        }

        return Command::SUCCESS;
    }
}
