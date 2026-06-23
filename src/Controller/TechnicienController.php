<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MaintenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TECHNICIEN')]
#[Route('/technicien')]
class TechnicienController extends AbstractController
{
    #[Route('', name: 'technicien_dashboard')]
    public function index(MaintenanceRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $tasks = $repo->findByTechnician($user->getId());

        $byType = [];
        foreach ($tasks as $task) {
            $byType[$task->getInterventionType()][] = $task;
        }

        return $this->render('technicien/index.html.twig', [
            'tasks'  => $tasks,
            'byType' => $byType,
        ]);
    }
}
