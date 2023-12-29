<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class CygnusDatabaseService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly ContextBrokerService $contextBrokerService
    )
    {
    }

    public function getData(): array
    {
        $username = $this->security->getUser()->getUserIdentifier();
        $connection = $this->entityManager->getConnection('cygnus');

        if ($connection->isConnected()) {
            $connection->close();
        }

        $params = array_merge($connection->getParams(),['dbname' => $username]);

        $connection->__construct(
            $params,
            $connection->getDriver(),
            $connection->getConfiguration(),
            $connection->getEventManager()
        );

        $result = [];

        try {
            $connection->connect();
            $entities = json_decode($this->contextBrokerService->getEntities($username), true);
            $tables = [];

            if($entities) {
                foreach ($entities as $entity) {
                    if (isset($entity['organization'])) {
                        $organization = strtolower($entity['organization']['value']);
                        $id = str_replace(':', '_', $entity['id']);
                        $type = $entity['type'];
                        $tables[] = $organization . '_' . $id . '_' . $type;
                    }
                }

                $query = array_map(function ($table) {
                    return "SELECT * FROM `{$table}` WHERE `attrName` = 'status'";
                }, $tables);
                $query = implode(' UNION ALL ', $query);

                $result = $connection->executeQuery($query)->fetchAllAssociative();
            }
        } catch (\Doctrine\DBAL\Exception $e) {
            // Manejo de excepciones
        }

        return $result;

    }
}