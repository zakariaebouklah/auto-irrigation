<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Entity\Parcel;
use App\Repository\ParcelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ParcelController extends AbstractController
{
    /**
     * url structure : http://localhost:8000/api/new/parcel/farm/{id}
     */

    #[IsGranted("FARM_VIEW", "farm")]
    #[Route('/api/new/parcel/farm/{id}', name: "app_new_parcel", methods: ['POST'])]
    public function addNewParcel(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        ParcelRepository $repository,
        Farm $farm
    ):JsonResponse
    {
        $data = $request->getContent();

        try {

            /**
             * @var Parcel $parcel
             */
            $parcel = $serializer->deserialize($data, Parcel::class, "json");

            $p = $repository->findOneBy(['farm' => $farm, 'parcelName' => $parcel->getParcelName()]);

            if($p != null)
            {
                return $this->json([
                    "message" => "Parcel Already Existing... Try Adding new one."
                ], 406);
            }

        }
        catch(NotEncodableValueException $ex)
        {
            return $this->json([
                "error" => $ex->getMessage()
            ], 400);
        }

        $farm->addParcel($parcel);

        $manager->persist($parcel);
        $manager->flush();

        return $this->json(["message" => "new parcel has been added."], 201);
    }

    /**
     * url structure : http://localhost:8000/api/edit/parcel/{id}
     */

    #[IsGranted("PARCEL_EDIT", "parcel")]
    #[Route('/api/edit/parcel/{id}', name: "app_edit_parcel", methods: ['PUT'])]
    public function editParcel(
        Request $request,
        EntityManagerInterface $manager,
        Parcel $parcel
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data['parcelName']))
            $parcel->setParcelName($data['parcelName']);

        if(isset($data['area']))
            $parcel->setArea($data['area']);

        $manager->flush();

        return $this->json(["message" => "parcel updated successfully."], 200);
    }

    /**
     * url structure : http://localhost:8000/api/delete/parcel/{id}
     */

    #[IsGranted('PARCEL_DELETE', "parcel")]
    #[Route('/api/delete/parcel/{id}', name: "app_delete_parcel", methods: ['DELETE'])]
    public function deleteParcel(
        EntityManagerInterface $manager,
        Parcel $parcel
    ): JsonResponse
    {

        $manager->remove($parcel);
        $manager->flush();

        return $this->json(["message" => "parcel deleted successfully."], 200);
    }

    /**
     * url structure : http://localhost:8000/api/all-parcels/farm/{id}
     */

    #[IsGranted("FARM_VIEW", "farm")]
    #[Route('/api/all-parcels/farm/{id}', name: "app_get_all_parcels", methods: ['GET'])]
    public function getParcelsOfFarm(Farm $farm): JsonResponse
    {
        $parcels = $farm->getParcels();

        if(count($parcels) == 0)
            return $this->json(['message' => "no parcels yet..."], 200);

        return $this->json(['parcels' => $parcels], 200, [], ["groups" => ["farm", "parcel", "soil", "crop", "farmer"]]);
    }

    /**
     * url structure : http://localhost:8000/api/parcel/{id}
     */

    #[IsGranted("PARCEL_VIEW", "parcel")]
    #[Route('/api/parcel/{id}', name: "app_get_specific_parcel", methods: ['GET'])]
    public function getSpecificParcelOfFarm(Parcel $parcel): JsonResponse
    {
        return $this->json(['parcel' => $parcel], 200, [], ["groups" => ["farm", "parcel", "soil", "crop", "farmer"]]);
    }
}
