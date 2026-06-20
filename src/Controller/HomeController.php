<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\EquipmentRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        $mackie = $equipmentRepository->findOneBy(['reference' => 'AUDIO-001']);
        $shure = $equipmentRepository->findOneBy(['reference' => 'AUDIO-002']);
        $epson = $equipmentRepository->findOneBy(['reference' => 'VIDEO-001']);

        return $this->render('home/index.html.twig', [
            'mackie' => $mackie,
            'shure' => $shure,
            'epson' => $epson,
        ]);
    }
}
