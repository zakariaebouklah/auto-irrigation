<?php

namespace App\Controller;

use App\Entity\Farmer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ResetPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    #[Route('/api/reset-password', name: 'app_reset_password')]
    public function sendOtpCode(Request $request, UserPasswordHasherInterface $userPasswordHasher
        ,SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $data = $serializer->decode($data, 'json');
        $newPassword = $data['new_password'];
        $phoneNumber = $data['phone_number'];
        $countryCode = $data['country_code'];

        $user = $this->entityManager->getRepository(Farmer::class)->findOneBy([
            'phone' => $phoneNumber,
            'countryCode' => $countryCode
        ]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        //hash the new password
        $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Password reset successfully'], 200);
    }
}
