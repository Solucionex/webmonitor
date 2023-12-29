<?php

namespace App\Command;

use App\Service\ContextBrokerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:context-broker:subscribe',
    description: 'Add a short description for your command',
)]
class ContextBrokerSubscribeCommand extends Command
{
    public function __construct(
        private readonly ContextBrokerService $contextBrokerService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('service', InputArgument::OPTIONAL, 'Fiware Service', 'openiot')
            ->addArgument('service-path', InputArgument::OPTIONAL, 'Fiware ServicePath', '/')
            ->addArgument('description', InputArgument::REQUIRED, 'Description')
            ->addArgument('url', InputArgument::REQUIRED, 'Endpoint Url')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $service = $input->getArgument('service');
        $servicePath = $input->getArgument('service-path');
        $description = $input->getArgument('description');
        $url = $input->getArgument('url');

        $response = $this->contextBrokerService->createSubscription($description, $url, $service, $servicePath);

        if($response->getStatusCode() == 200){
            $io->success('A new subscription has been created successfully.');
            return Command::SUCCESS;

        }else{
            $io->error('There has been an error during subscription processs. Please, try again.');
            return Command::FAILURE;
        }


    }
}
