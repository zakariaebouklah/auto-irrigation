<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Entity\Farmer;
use App\Entity\Soil;
use App\Repository\SoilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SoilController extends AbstractController
{
    #[IsGranted("SOIL_EDIT", "soil")]
    #[Route('/api/edit-custom-soil/{id}', name: "app_edit_custom_soil", methods: ['PUT'])]
    public function editCustomSoil(
        Request $request,
        Soil $soil,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data["type"]))
            $soil->setType($data["type"]);

        if(isset($data["paw"]))
            $soil->setPaw($data["paw"]);

        if(isset($data["depth"]))
            $soil->setDepth($data["depth"]);

        $manager->flush();

        return $this->json(["message" => "soil updated successfully."], 200);
    }

    #[IsGranted("SOIL_DELETE", "soil")]
    #[Route('/api/delete-custom-soil/{id}', name: "app_delete_custom_soil", methods: ['DELETE'])]
    public function deleteCustomSoil(
        Soil $soil,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $manager->remove($soil);
        $manager->flush();

        return $this->json(["message" => "soil deleted successfully."], 200);
    }

    //all standard and custom soils

    #[Route('/api/all-standard-soils', name: 'app_all_standard_soils', methods: ['GET'])]
    public function getAllStandardSoils(
        SoilRepository $repository
    ): JsonResponse
    {
        /**
         * @var Crop[] $standardSoils
         */
        $standardSoils = $repository->findBy(['owner' => null]);

        return $this->json(["standard" => $standardSoils], 200, [], ["groups" => ["soil"]]);
    }

    #[Route('/api/all-custom-soils', name: 'app_all_custom_soils', methods: ['GET'])]
    public function getAllCustomSoils(
        SoilRepository $repository
    ): JsonResponse
    {
        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();

        /**
         * @var Crop[] $customSoils
         */
        $customSoils = $repository->findBy(['owner' => $farmer]);

        return $this->json(["custom" => $customSoils], 200, [], ["groups" => ["soil", "farmer"]]);
    }
}
