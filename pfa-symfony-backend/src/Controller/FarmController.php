<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Entity\Farmer;
use App\Repository\FarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class FarmController extends AbstractController
{
    #[Route('/api/new/farm', name: "app_new_farm", methods: ['POST'])]
    public function addNewFarm(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        FarmRepository $repository
    ): JsonResponse
    {
        $data = $request->getContent();

        try {
            /**
             * @var Farm $farm
             */
            $farm = $serializer->deserialize($data, Farm::class, "json");

            // disable the case when the farmer tries to enter the same farm multiple times.

            $f = $repository->findOneBy(['farmer' => $this->getUser(), 'farmName' => $farm->getFarmName()]);

            if($f != null)
            {
                return $this->json([
                    "message" => "Farm Already Existing... Try Adding new one."
                ], 406);
            }
        }
        catch(NotEncodableValueException $ex)
        {
            return $this->json([
                "error" => $ex->getMessage()
            ], 400);
        }

        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();

        $farmer->addFarm($farm);

        $manager->persist($farm);
        $manager->flush();

        return $this->json([
            'message' => "new farm with name {$farm->getFarmName()} has been created."
        ], 201);
    }

    #[IsGranted("FARM_EDIT", "farm")]
    #[Route('/api/edit/farm/{id}', name: "app_edit_farm", methods: ['PUT'])]
    public function editFarm(
        Request $request,
        EntityManagerInterface $manager,
        Farm $farm
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if(isset($data['farmName']))
            $farm->setFarmName($data["farmName"]);

        if(isset($data['area']))
            $farm->setArea($data["area"]);

        if(isset($data['weatherStation']))
            $farm->setWeatherStation($data["weatherStation"]);

        $manager->flush();

        return $this->json(["message" => "farm updated successfully."], 200);

    }

    #[IsGranted("FARM_DELETE", "farm")]
    #[Route('/api/delete/farm/{id}', name: "app_delete_farm", methods: ['DELETE'])]
    public function deleteFarm(
        EntityManagerInterface $manager,
        Farm $farm
    ): JsonResponse
    {
        $manager->remove($farm);
        $manager->flush();

        return $this->json(["message" => "farm deleted successfully."], 200);
    }

    #[Route('/api/all-farms', name: "app_get_all_farms", methods: ['GET'])]
    public function getFarms(FarmRepository $repository): JsonResponse
    {
        /**
         * @var Farm[] $farms
         */
        $farms = $repository->findBy(['farmer' => $this->getUser()]);

        if(count($farms) == 0)
            return $this->json(["message" => "You don't got any farms yet."], 200);

        return $this->json(["farms" => $farms], 200, [], ["groups" => ["farm", "parcel", "soil", "crop", "farmer"]]);
    }

    #[IsGranted("FARM_VIEW", 'farm')]
    #[Route('/api/farm/{id}', name: "app_get_farm", methods: ['GET'])]
    public function getSpecificFarm(
        Farm $farm
    ): JsonResponse
    {
        return $this->json(["farm" => $farm], 200, [], ["groups" => ["farm", "parcel", "soil", "crop", "farmer"]]);
    }
}
