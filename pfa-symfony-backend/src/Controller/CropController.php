<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Entity\Farmer;
use App\Repository\CropRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CropController extends AbstractController
{
    /**
     * url structure : http://localhost:8000/api/edit-custom-crop/{id}
     */

    #[IsGranted("CROP_EDIT", "crop")]
    #[Route('/api/edit-custom-crop/{id}', name: "app_edit_custom_crop", methods: ['PUT'])]
    public function editCustomCrop(
        Request $request,
        Crop $crop,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data["cropName"]))
            $crop->setCropName($data["cropName"]);

        if(isset($data["fad"]))
            $crop->setFad($data["fad"]);

        if(isset($data["maxRootDepth"]))
            $crop->setMaxRootDepth($data["maxRootDepth"]);

        if(isset($data["harvestedGreen"]))
            $crop->setHarvestedGreen($data["harvestedGreen"]);

        if(isset($data["stages"]))
            $crop->setStages($data["stages"]);

        if(isset($data["sowDepth"]))
            $crop->setSowDepth($data["sowDepth"]);

        $manager->flush();

        return $this->json(["message" => "crop updated successfully."], 200);

    }

    #[IsGranted("CROP_DELETE", "crop")]
    #[Route('/api/delete-custom-crop/{id}', name: "app_delete_custom_crop", methods: ['DELETE'])]
    public function deleteCustomCrop(
        Crop $crop,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $manager->remove($crop);
        $manager->flush();

        return $this->json(["message" => "crop deleted successfully."], 200);
    }

    //all standard and custom crops

    #[Route('/api/all-standard-crops', name: 'app_all_standard_crops', methods: ['GET'])]
    public function getAllStandardCrops(
        CropRepository $repository
    ): JsonResponse
    {
        /**
         * @var Crop[] $standardCrops
         */
        $standardCrops = $repository->findBy(['owner' => null]);

        return $this->json(["standard" => $standardCrops], 200, [], ["groups" => ["crop"]]);
    }

    #[Route('/api/all-custom-crops', name: 'app_all_custom_crops', methods: ['GET'])]
    public function getAllCustomCrops(
        CropRepository $repository
    ): JsonResponse
    {
        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();

        /**
         * @var Crop[] $customCrops
         */
        $customCrops = $repository->findBy(['owner' => $farmer]);

        return $this->json(["custom" => $customCrops], 200, [], ["groups" => ["crop", "farmer"]]);
    }
}
