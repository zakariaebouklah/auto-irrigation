<?php
namespace App\Service;

use App\Entity\Farmer;
use App\Entity\OtpCode;
use App\Repository\OtpCodeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class OtpService
{
    private const EXPIRATION_TIME = 'PT5M';
    private OtpCodeRepository $otpCodeRepository;
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $JWTManager;
    private AuthenticationSuccessHandlerInterface $authenticationSuccessHandler;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(OtpCodeRepository $otpCodeRepository,
                                EntityManagerInterface  $entityManager,
                                AuthenticationSuccessHandlerInterface $authenticationSuccessHandler,
                RequestStack $requestStack)
    {
        date_default_timezone_set('UTC');
        $this->otpCodeRepository = $otpCodeRepository;
        $this->entityManager = $entityManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->requestStack = $requestStack;

    }

    /**
     * @throws Exception
     */
    /**
     * @param string $phoneNumber
     * @return OtpCode
     * @throws Exception
     */
    public function generateOtpCode(string $phoneNumber): OtpCode
    {
        $code = rand(1000, 9999);

        $expirationDate = (new DateTimeImmutable())
            ->add(new \DateInterval(self::EXPIRATION_TIME));

        $otp = new OtpCode();
        $otp->setPhoneNumber($phoneNumber);
        $otp->setCode($code);
        $otp->setExpirationDate($expirationDate);
        $otp->setCreatedAt(new DateTimeImmutable());

        try {
            $this->entityManager->persist($otp);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new Exception('Error saving OTP code: ' . $e->getMessage());
        }

        return $otp;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyOtpCode(string $phoneNumber, string $countryCode, string $code)
    {
        $otp = $this->otpCodeRepository->findOneBy(['phoneNumber' => $countryCode.$phoneNumber], ['createdAt' => 'DESC']);
        if (!$otp) {
            return ['error' => 'OTP code not found.'];
        }

        $now = new \DateTimeImmutable();
        if ($otp->getExpirationDate() < $now) {
            return ['error' => 'OTP code expired.'];
        }

        if ($otp->getCode() !== $code) {
            return ['error' => 'Invalid OTP code.'];
        }
        //Check if user is already registered by phone number
        //check if exists in the database where have the phone + country code
        $user = $this->entityManager->getRepository(Farmer::class)->findOneBy(['phone' => $phoneNumber, 'countryCode' => $countryCode]);
        if($user){
            //User already registered, create the token and return it with the user data
            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        }
        //User not registered, return just a success message and continue with the registration process
        return new JsonResponse(['message' => 'OTP code verified, user not registered.'], Response::HTTP_PERMANENTLY_REDIRECT);
    }

    /**
     * @throws Exception
     */
    public function registerPhoneUser($data){
        $firstName = $data['firstName'] ?? null;
        $lastName = $data['lastName'] ?? null;
        $phoneNumber = $data['phone'] ?? null;
        $countryCode = $data['countryCode'] ?? null;
        $email = $data['email'] ?? null;

        if (!$firstName || !$lastName || !$phoneNumber) {
            throw new \InvalidArgumentException('First name, last name and phone number are required.');
        }

        $farmer = $this->entityManager->getRepository(Farmer::class)->findOneBy(['phone' => $phoneNumber]);
        if ($farmer) {
            throw new \InvalidArgumentException('Phone number already in use.');
        }

        if ($email) {
            $farmer = $this->entityManager->getRepository(Farmer::class)->findOneBy(['email' => $email]);
            if ($farmer) {
                throw new \InvalidArgumentException('Email already in use.');
            }
        }

        $farmer = new Farmer();
        $farmer->setFirstName($firstName);
        $farmer->setLastName($lastName);
        $farmer->setPhone($phoneNumber);
        $farmer->setCountryCode($countryCode);
        if($email)
            $farmer->setEmail($email);
        $farmer->setPassword('');

        try {
            $this->entityManager->persist($farmer);
            $this->entityManager->flush();

           return $this->authenticationSuccessHandler->handleAuthenticationSuccess($farmer);

        } catch (ORMException $e) {
            throw new Exception('Error saving farmer: ' . $e->getMessage());
        }
    }
}