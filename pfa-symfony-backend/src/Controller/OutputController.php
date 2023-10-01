<?php

namespace App\Controller;

use App\Entity\Farmer;
use App\Repository\OutputRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OutputController extends AbstractController
{

    #[Route('/api/schedules/user/all', name: "app_get_users_schedules", methods: ['GET'])]
    public function getAllSchedules(OutputRepository $repository): JsonResponse
    {
        /**
         * @var Farmer $farmer
         */
        $farmer = $this->getUser();

        $schedules = $repository->getAllSchedulesOfUser($farmer);

        if(count($schedules) == 0)
            return $this->json([
                "message" => "You don't have any irrigation schedules yet."
            ], 200);

        return $this->json(['schedules' => $schedules], 200);
    }

    /**
     * http://localhost:8000/api/schedule/steps/crop/3/soil/3
     */

    #[Route('/api/schedule/steps/crop/{id1}/soil/{id2}', name: "app_get_steps_of_schedule", methods: ['GET'])]
    public function getStepsOfASchedule(
        Request $request,
        OutputRepository $outputRepository
    ):JsonResponse
    {
        $c_id = intval($request->get('id1'));
        $s_id = intval($request->get('id2'));

//        dd($f_id, $c_id, $s_id);

        /**
         * @var Farmer $currentFarmer
         */
        $currentFarmer = $this->getUser();

        $outputs = $outputRepository->getAllStepsOfASchedule($currentFarmer->getId(), $c_id, $s_id);

        if(count($outputs) == 0)
            return $this->json([
                "message" => "You don't have any schedule with this specifications here."
            ], 404);

        return $this->json(["irrigation_steps" => $outputs], 200, [], [
            "groups" => ["output", "crop", "soil", "farmer"]
        ]);
    }
}
