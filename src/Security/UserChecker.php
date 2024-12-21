<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface{

    
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // if(!$user->getCompany()->isActive()){
        //     throw new CustomUserMessageAccountStatusException('Login impossible, votre entreprise est désactivée.');   
        // }
    }

    public function checkPostAuth(UserInterface $user):void
    {
        if (!$user instanceof User) {
            return;
        }

    }
}