<?php

namespace App\Security\Voter;

use App\Entity\Crop;
use App\Entity\Farmer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CropVoter extends Voter
{
    public const EDIT = 'CROP_EDIT';
    public const DELETE = 'CROP_DELETE';

    protected function supports(string $attribute, mixed $crop): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $crop instanceof \App\Entity\Crop;
    }

    protected function voteOnAttribute(string $attribute, mixed $crop, TokenInterface $token): bool
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
                return $this->canEditCustomCrop($farmer, $crop);
            case self::DELETE:
                // logic to determine if the user can DELETE
                /**
                 * @var Farmer $farmer
                 */
                return $this->canDeleteCustomCrop($farmer, $crop);
        }

        return false;
    }

    private function canEditCustomCrop(Farmer $farmer, Crop $crop): bool
    {
        return $farmer->getCrops()->contains($crop);
    }

    private function canDeleteCustomCrop(Farmer $farmer, Crop $crop): bool
    {
        return $this->canEditCustomCrop($farmer, $crop);
    }
}
