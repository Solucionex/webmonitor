<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\IoTAgentService;
use App\Service\NetworkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:devices:update',
    description: 'Update the status value of all websites',
)]
class DevicesUpdateCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly IoTAgentService $ioTAgentService,
        private readonly NetworkService $networkService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::OPTIONAL, 'The username of a user of the application');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        if($username) {
            $users = $this->userRepository->findBy(['username' => $username]);
        }else{
            $users = $this->userRepository->findAll();
        }

        foreach ($users as $user) {
            $username = $user->getUserIdentifier();
            $response = $this->ioTAgentService->getServices($username, '/*');
            $services = json_decode($response,true)['services'];

            foreach ($services as $service) {
                $response = $this->ioTAgentService->getDevices($username, $service['subservice']);
                $devices = json_decode($response, true)['devices'];

                foreach ($devices as $device) {
                    $url = $device['static_attributes'][2]['value'];
                    $port = 80;

                    if(str_starts_with($url,'https')){
                        $url = str_replace('https','ssl', $url);
                        $port = 443;
                    }

                    $status = $this->networkService->ping($url,$port)->getStatusCode();

                    $response = $this->ioTAgentService->updateDevice(
                        $service['apikey'],
                        $device['device_id'],
                        $status == 200 ? 1 : 0
                    );

                    if($response->getStatusCode() != 200){
                        $device_id = $device['device_id'];
                        $io->error("Has received an error in updating process with device $device_id:" . PHP_EOL . $response->getContent());
                    }
                }
            }
        }
        $io->success("All devices has been updated");
        return Command::SUCCESS;
    }
}
