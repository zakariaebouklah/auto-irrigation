<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Entity\Farmer;
use App\Entity\Output;
use App\Entity\Parcel;
use App\Entity\Soil;
use App\Repository\CropRepository;
use App\Repository\FarmerRepository;
use App\Repository\FarmRepository;
use App\Repository\ParcelRepository;
use App\Repository\SoilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InputsController extends AbstractController
{
    /**
     * url structure : http://localhost:8000/api/standard-crop/custom-soil/parcel/{id}
     */

    #[IsGranted("PARCEL_EDIT", "parcel")]
    #[Route('/api/standard-crop/custom-soil/parcel/{id}', name: "app_mix_1", methods: ['POST'])]
    public function standardCropCustomSoilSelection(
        Request $request,
        EntityManagerInterface $manager,
        CropRepository $cropRepository,
        SoilRepository $soilRepository,
        FarmerRepository $farmerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        HttpClientInterface $client,
        Parcel $parcel
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            /**
             * @var Crop $crop
             */
            $crop = $cropRepository->findOneBy(['cropName' => $data['cropName']]);

            $strSoil = json_encode($data['soil']);
            /**
             * @var Soil $soil
             */
            $soil = $serializer->deserialize($strSoil, Soil::class, "json");
            $errors = $validator->validate($soil);

            if(count($errors) > 0){
                return $this->json([
                    "errors" => $errors
                ]);
            }

        }catch(NotEncodableValueException $exception){
            return $this->json([
                "error" => $exception->getMessage()
            ], 400);
        }

        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();
        $farmer->addSoil($soil);

        $parcel->setSoil($soil);
        $parcel->setCrop($crop);

        $manager->persist($soil);
        $manager->flush();

        // sending data to python :

        $res = $this->sendRequestToFastAPI(
            $client,
            $serializer,
            $manager,
            $crop,
            $soil,
            $farmerRepository,
            $soilRepository,
            $cropRepository
        );

        if($res){
            return $this->json([
                "error" => $res
            ], 400);
        }

        return $this->json([
            "message" => "selection done.",
            "crop" => $crop,
            "soil" => $soil
        ], 201, [], ["groups" => ["crop", "soil", "farmer"]]);

    }

    /**
     * url structure : http://localhost:8000/api/custom-crop/standard-soil/parcel/{id}
     */

    #[IsGranted("PARCEL_EDIT", "parcel")]
    #[Route('/api/custom-crop/standard-soil/parcel/{id}', name: "app_mix_2", methods: ['POST'])]
    public function customCropStandardSoilSelection(
        Request $request,
        EntityManagerInterface $manager,
        SoilRepository $soilRepository,
        FarmerRepository $farmerRepository,
        CropRepository $cropRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        HttpClientInterface $client,
        Parcel $parcel
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            /**
             * @var Soil $soil
             */
            $soil = $soilRepository->findOneBy(['type' => $data['soilType']]);

            $strCrop = json_encode($data['crop']);
            /**
             * @var Crop $crop
             */
            $crop = $serializer->deserialize($strCrop, Crop::class, "json");
            $errors = $validator->validate($crop);

            if(count($errors) > 0){
                return $this->json([
                    "errors" => $errors
                ]);
            }

        }catch(NotEncodableValueException $exception){
            return $this->json([
                "error" => $exception->getMessage()
            ], 400);
        }

        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();
        $farmer->addCrop($crop);

        $parcel->setCrop($crop);
        $parcel->setSoil($soil);

        $manager->persist($crop);
        $manager->flush();

        // sending data to python

        $res = $this->sendRequestToFastAPI(
            $client,
            $serializer,
            $manager,
            $crop,
            $soil,
            $farmerRepository,
            $soilRepository,
            $cropRepository
        );

        if($res){
            return $this->json([
                "error" => $res
            ], 400);
        }

        return $this->json([
            "message" => "selection done.",
            "crop" => $crop,
            "soil" => $soil
        ], 201, [], ["groups" => ["crop", "soil", "farmer"]]);


    }

    /**
     * url structure : http://localhost:8000/api/standard/selection/parcel/{id}
     */

    #[IsGranted("PARCEL_EDIT", "parcel")]
    #[Route('/api/standard/selection/parcel/{id}', name: 'app_standards', methods: ['POST'])]
    public function standardSelection(
        Request $request,
        CropRepository $cropRepository,
        SoilRepository $soilRepository,
        FarmerRepository $farmerRepository,
        HttpClientInterface $client,
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        Parcel $parcel
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /**
         * @var Crop $crop
         */
        $crop = $cropRepository->findOneBy(['cropName' => $data["cropName"]]);

        /**
         * @var Soil $soil
         */
        $soil = $soilRepository->findOneBy(['type' => $data["soilType"]]);

        $parcel->setCrop($crop);
        $parcel->setSoil($soil);

        // sending data to python :

        $res = $this->sendRequestToFastAPI(
            $client,
            $serializer,
            $manager,
            $crop,
            $soil,
            $farmerRepository,
            $soilRepository,
            $cropRepository
        );

        if($res){
            return $this->json([
                "error" => $res
            ], 400);
        }

        return $this->json([
            "message" => "selection done.",
            "crop" => $crop,
            "soil" => $soil
        ], 200, [], ["groups" => ["crop", "soil", "farmer"]]);

    }

    /**
     * url structure : http://localhost:8000/api/custom/selection/parcel/{id}
     */

    #[IsGranted("PARCEL_EDIT", "parcel")]
    #[Route('/api/custom/selection/parcel/{id}', name: 'app_customs', methods: ['POST'])]
    public function customSelection(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        HttpClientInterface $client,
        FarmerRepository $farmerRepository,
        CropRepository $cropRepository,
        SoilRepository $soilRepository,
        Parcel $parcel
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            $strCrop = json_encode($data["crop"]);
            /**
             * @var Crop $crop
             */
            $crop = $serializer->deserialize($strCrop, Crop::class, "json");
            $errors1 = $validator->validate($crop);

            $strSoil = json_encode($data["soil"]);
            /**
             * @var Soil $soil
             */
            $soil = $serializer->deserialize($strSoil, Soil::class, "json");
            $errors2 = $validator->validate($soil);

            if(count($errors1) > 0 || count($errors2) > 0){
                return $this->json(["errors" => [$errors1, $errors2]]);
            }

        }
        catch(NotEncodableValueException $exception){
            return $this->json([
                "error" => $exception->getMessage()
            ], 400);
        }

        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();

        $farmer->addCrop($crop);
        $farmer->addSoil($soil);

        $manager->persist($crop);
        $manager->persist($soil);
        $manager->flush();

        $parcel->setCrop($crop);
        $parcel->setSoil($soil);

        // sending data to python

        $res = $this->sendRequestToFastAPI(
            $client,
            $serializer,
            $manager,
            $crop,
            $soil,
            $farmerRepository,
            $soilRepository,
            $cropRepository
        );

        if($res){
            return $this->json([
                "error" => $res
            ], 400);
        }

        return $this->json([
            "message" => "custom selection done.",
            "crop" => $crop,
            "soil" => $soil
        ], 201, [], ["groups" => ["crop", "soil", "farmer"]]);

    }

    private function sendRequestToFastAPI(
        HttpClientInterface $client, SerializerInterface $serializer, EntityManagerInterface $manager,
        Crop $c, Soil $s,
        FarmerRepository $farmerRepository, SoilRepository $soilRepository, CropRepository $cropRepository
    ): ?string{
        try {
            $response = $client->request(
                'POST',
                'http://python:8080/fastapi/make/calculations',
                [
                    'json' => [
                        'crop' => json_decode(
                            $serializer->serialize(
                                $c, "json", ["groups" => ["crop"]]
                            ), true
                        ),
                        'soil' => json_decode(
                            $serializer->serialize(
                                $s, "json", ["groups" => ["soil"]]
                            ), true
                        ),
                        'user' => json_decode(
                            $serializer->serialize(
                                $this->getUser(), "json", ["groups" => ["farmer"]]
                            ), true)
                    ]
                ]
            );

            if($response->getStatusCode() == 200){

                $result = $response->getContent();

                try {
                    /**
                     * @var Output $output
                     */
                    $output = $serializer->deserialize($result, Output::class, "json");

                    $result = json_decode($response->getContent(), true);

                    /**
                     * @var Farmer $farmer
                     */
                    $farmer = $farmerRepository->find($result['owner_id']);
                    /**
                     * @var Crop $crop
                     */
                    $crop = $cropRepository->find($result['crop_id']);
                    /**
                     * @var Soil $soil
                     */
                    $soil = $soilRepository->find($result['soil_id']);

                    $output->setOwner($farmer);
                    $output->setCrop($crop);
                    $output->setSoil($soil);
                    $output->setDateOfCalculations(new \DateTimeImmutable());

                    $manager->persist($output);
                    $manager->flush();

                }
                catch (NotEncodableValueException $e)
                {
                    return $e->getMessage();
                }
            }
            else
                throw new \Exception("CONTENT : " . $response->getContent());
        }
        catch( TransportExceptionInterface|
        ClientExceptionInterface|
        RedirectionExceptionInterface|
        ServerExceptionInterface | \Exception
        $e
        ){
            return $e->getMessage();
        }

        return null;
    }
}
