<?php

namespace App\Controller;

use App\Form\EntityFormType;
use App\Service\ContextBrokerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntityController extends AbstractController
{
    public function __construct(
        private ContextBrokerService $contextBrokerService,
    ) {
    }

    #[Route('/fiware/entidad/nueva', name: 'fiware_entity_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(EntityFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entity = [
                        'id' => $data['id'],
                        'type' => $data['type'],
                    ];
            foreach(explode("\n",$data['fields']) as $str)
            {
                $values=explode(",",$str);
                $entity[trim($values[0])] = [
                    'type' => trim($values[1]),
                    'value' => trim($values[2]),
                ];
            }

            $response = $this->contextBrokerService->createEntity($entity);

            if ($response->getStatusCode() == '200') {
                $this->addFlash(
                    'success',
                    "La entidad se ha creado correctamente"
                );
            } else {
                $message = $response->getContent();
                $this->addFlash(
                    'error',
                    "Se ha detectado un error al eliminar la entidad: ($message)"
                );
            }

            return $this->redirectToRoute('app_fiware_index');
        }
    }

    #[Route('/fiware/entidad/eliminar/{id}', name: 'fiware_device_delete')]
    public function delete(Request $request, string $id): Response
    {
        $response = $this->contextBrokerService->deleteEntity($id);

        if ($response->getStatusCode() == '200') {
            $this->addFlash(
                'success',
                "El dispositivo <strong>$id</strong> se ha eliminado correctamente"
            );
        } else {
            $message = $response->getContent();
            $this->addFlash(
                'error',
                "Se ha detectado un error al eliminar el dispositivo <strong>$id</strong>: ($message)"
            );
        }

        return $this->redirectToRoute('app_fiware_index');
    }
}
