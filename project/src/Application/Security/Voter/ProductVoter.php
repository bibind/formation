<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Domain\Product;
use App\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProductVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['PRODUCT_EDIT', 'PRODUCT_VIEW'], true) && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        return $user->isAdmin() || $subject->owner()->id() === $user->id();
    }
}
