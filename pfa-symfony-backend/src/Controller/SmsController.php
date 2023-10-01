<?php

namespace App\Controller;
use App\Service\OtpService;
use App\Service\SmsService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class SmsController extends AbstractController
{
    private OtpService $otpService;
    private SmsService $smsService;

    public function __construct(OtpService $otpService, SmsService $smsService)    {
        $this->otpService = $otpService;
        $this->smsService = $smsService;
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/send-verification-code', name: 'send_verification_code', methods: ['POST'])]
    public function sendSms(Request $request,SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $data = $serializer->decode($data, 'json');
        $phoneNumber = $data['phone_number'];
        $countryCode = $data['country_code'];
        if (empty($phoneNumber)) {
            return new JsonResponse(['error' => 'Receiver phone number is required.'], 400);
        }
        $phoneNumber = $countryCode . $phoneNumber;
        $otp = $this->otpService->generateOtpCode($phoneNumber);

        $message = sprintf('Votre code d\'accès pour EauWise est: %s', $otp->getCode());
        $this->smsService->sendSms($phoneNumber, $message);

        return new JsonResponse(['message' => 'SMS sent'], 200);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/api/verify-otp', name: 'verify_otp', methods: ['POST'])]
    public function verifyOtp(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $data = $serializer->decode($data, 'json');
        $phoneNumber = $data['phone_number'];
        $countryCode = $data['country_code'];
        $code = $data['code'];
        if (empty($phoneNumber) || empty($code)) {
            return new JsonResponse(['error' => 'Phone number and code are required.'], 400);
        }
        $result = $this->otpService->verifyOtpCode($phoneNumber, $countryCode, $code);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        if (isset($result['error'])) {
            return new JsonResponse($result, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

}
