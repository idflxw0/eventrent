<?php

namespace App\Controller\Api;

use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1')]
class EquipmentApiController extends AbstractController
{
    #[Route('/equipments', name: 'api_equipment_list', methods: ['GET'])]
    public function list(EquipmentRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $equipment = iterator_to_array($repo->search(sort: 'name', limit: 100));

        $json = $serializer->serialize($equipment, 'json', [
            'groups' => ['equipment:list'],
        ]);

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/equipments/{id}', name: 'api_equipment_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, EquipmentRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $equipment = $repo->findOneWithRelations($id);

        if (!$equipment) {
            return new JsonResponse(['error' => 'Equipment not found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($equipment, 'json', [
            'groups' => ['equipment:detail'],
        ]);

        return JsonResponse::fromJsonString($json);
    }
}
