<?php

namespace App\Security\Voter;

use App\Entity\Farmer;
use App\Entity\Parcel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ParcelVoter extends Voter
{
    public const EDIT = 'PARCEL_EDIT';
    public const DELETE = 'PARCEL_DELETE';
    public const VIEW = 'PARCEL_VIEW';

    protected function supports(string $attribute, mixed $parcel): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $parcel instanceof \App\Entity\Parcel;
    }

    protected function voteOnAttribute(string $attribute, mixed $parcel, TokenInterface $token): bool
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
                 return $this->canEditParcel($farmer, $parcel);
            case self::DELETE:
                // logic to determine if the user can DELETE
                /**
                 * @var Farmer $farmer
                 */
                return $this->canDeleteParcel($farmer, $parcel);
            case self::VIEW:
                // logic to determine if the user can VIEW
                /**
                 * @var Farmer $farmer
                 */
                return $this->canViewParcel($farmer, $parcel);
        }

        return false;
    }

    private function canEditParcel(Farmer $farmer, Parcel $parcel): bool
    {
        return $farmer->getFarms()->contains($parcel->getFarm());
    }

    private function canDeleteParcel(Farmer $farmer, Parcel $parcel): bool
    {
        return $this->canEditParcel($farmer, $parcel);
    }

    private function canViewParcel(Farmer $farmer, Parcel $parcel): bool
    {
        return $this->canEditParcel($farmer, $parcel);
    }

}
