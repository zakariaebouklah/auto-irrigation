<?php

namespace App\Controller;

use App\Entity\Farmer;
use App\Repository\FarmerRepository;
use App\Service\OtpService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    private OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager,
        FarmerRepository $repository,
        SerializerInterface $serializer,
        JWTTokenManagerInterface $tokenManager,
        EventDispatcherInterface $dispatcher
    ): JsonResponse
    {

        $data = $request->getContent();

        /**
         * the try--catch bloc is used to determine if the data we got from request (body) is Encodable
         * i.e. in a good json format...
         */

        try {

            /**
             * @var Farmer $farmer
             */
            $farmer = $serializer->deserialize($data, Farmer::class, "json");

            /**
             * case where the user(farmer) already registered re-register...
             */

            //$alreadyExistingUser has the same email or phone number If email is not in use
            //then $alreadyExistingUser will be null.
            $existingEmailUser = $repository->findOneBy(["email" => $farmer->getEmail()]);
            $existingPhoneUser = $repository->findOneBy(["phone" => $farmer->getPhone()]);
            if($existingEmailUser != null || $existingPhoneUser != null)
            {
                return $this->json([
                    "message" => "Account already exists..."
                ], 406);
            }


        } catch(NotEncodableValueException $ex)
        {
            return $this->json([
                "error_msg" => $ex->getMessage()
            ], 400);
        }

        //Hashing the password before persisting data into mySQL db.

        $farmer->setPassword($hasher->hashPassword($farmer, $farmer->getPassword()));

        $manager->persist($farmer);
        $manager->flush();

        //Generating the jw-token programmatically in registration step.
        //inspect jwt classes & services available : php bin/console debug:container jwt.
        $jsonResponse = new JsonResponse();

        $event = new AuthenticationSuccessEvent(
            ["token" => $tokenManager->create($farmer)],
            $farmer,
            $jsonResponse
        );

        $dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        $jsonResponse->setData(array_merge($event->getData(), [
            'message' => 'New farmer has been registered to the app...'
        ]));
        $jsonResponse->setStatusCode(Response::HTTP_CREATED);

        return $jsonResponse;
    }

    #[Route('/api/verify-otp/continue', name: 'register_after_otp', methods: ['POST'])]
    public function registerAfterOtp(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->decode($request->getContent(), 'json');
        try{
            $response = $this->otpService->registerPhoneUser($data);
        }
        catch(\Exception $ex)
        {
            return $this->json([
                "error_msg" => $ex->getMessage()
            ], 400);
        }

        return $response;
    }

}
