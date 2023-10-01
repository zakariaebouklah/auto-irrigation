<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Namshi\JOSE\JWS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginSuccessHandler
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var UserInterface $user */
        $user = $event->getUser();

        $data = $event->getData();
        $data['user'] = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'countryCode' => $user->getCountryCode(),
            'roles' => $user->getRoles(),
        ];
        $event->setData($data);
    }
}