<?php

namespace App\Security\Voter;

use App\Entity\Farmer;
use App\Entity\Soil;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SoilVoter extends Voter
{
    public const EDIT = 'SOIL_EDIT';
    public const DELETE = 'SOIL_DELETE';

    protected function supports(string $attribute, mixed $soil): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $soil instanceof \App\Entity\Soil;
    }

    protected function voteOnAttribute(string $attribute, mixed $soil, TokenInterface $token): bool
    {
        $farmer = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$farmer instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                /**
                 * @var Farmer $farmer
                 */
                 return $this->canEditCustomSoil($farmer, $soil);
            case self::DELETE:
                // logic to determine if the user can DELETE
                /**
                 * @var Farmer $farmer
                 */
                return $this->canDeleteCustomSoil($farmer, $soil);
                break;
        }

        return false;
    }

    private function canEditCustomSoil(
        Farmer $farmer,
        Soil $soil
    ): bool
    {
        return $farmer->getSoils()->contains($soil);
    }

    private function canDeleteCustomSoil(
        Farmer $farmer,
        Soil $soil
    ): bool
    {
        return $this->canEditCustomSoil($farmer, $soil);
    }
}
