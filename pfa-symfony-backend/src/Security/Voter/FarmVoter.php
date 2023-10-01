<?php

namespace App\Security\Voter;

use App\Entity\Farm;
use App\Entity\Farmer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FarmVoter extends Voter
{
    public const EDIT = 'FARM_EDIT';
    public const DELETE = 'FARM_DELETE';
    public const VIEW = 'FARM_VIEW';

    protected function supports(string $attribute, mixed $farm): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $farm instanceof \App\Entity\Farm;
    }

    protected function voteOnAttribute(string $attribute, mixed $farm, TokenInterface $token): bool
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
                 return $this->canEditFarm($farm, $farmer);
            case self::DELETE:
                // logic to determine if the user can DELETE
                /**
                 * @var Farmer $farmer
                 */
                return $this->canDeleteFarm($farm, $farmer);
            case self::VIEW:
                // logic to determine if the user can VIEW
                /**
                 * @var Farmer $farmer
                 */
                return $this->canViewFarm($farm, $farmer);
        }

        return false;
    }

    private function canEditFarm(Farm $farm, Farmer $farmer): bool
    {
        return $farmer === $farm->getFarmer();
    }

    private function canDeleteFarm(Farm $farm, Farmer $farmer): bool
    {
        return $this->canEditFarm($farm, $farmer);
    }

    private function canViewFarm(Farm $farm, Farmer $farmer): bool
    {
        return $this->canEditFarm($farm, $farmer);
    }

}
