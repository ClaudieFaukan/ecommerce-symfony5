<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const EDIT = 'CAN_EDIT';

    /**
     * This voter is only concerned with the EDIT attribute and only supports objects of type Category.
     * 
     * @param string attribute The attribute is a string representing the attribute name. For example, if
     * you want to check if the user can edit a blog post, the attribute would be "edit".
     * @param subject The subject is the object you're securing. In this case, it's a Post object.
     * 
     * @return bool A boolean value.
     */
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT])
            && $subject instanceof \App\Entity\Category;
    }
    /**
     * If the user is the owner of the object, then they can edit it
     * 
     * @param string attribute The attribute to check. In this case, we're checking if the user can edit
     * the given post.
     * @param subject The subject of the vote. This is the object that the voter is voting on.
     * @param TokenInterface token The current security token.
     * 
     * @return bool A boolean value.
     */

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getOwner() === $user;
        }

        return false;
    }
}
