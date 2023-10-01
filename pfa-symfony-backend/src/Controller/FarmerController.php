<?php

namespace App\Controller;

use App\Repository\FarmerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class FarmerController extends AbstractController
{
    #[Route('/farmer', name: 'app_farmer')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FarmerController.php',
        ]);
    }

    #[Route('/api/farmer/all', name: 'app_farmer_all')]
    public function getAllFarmers(FarmerRepository $repository,
    SerializerInterface $serializer): JsonResponse
    {
       // return $this->json($repository->findAll());
        return new JsonResponse("This is the list of all farmers", Response::HTTP_OK);
    }
}
