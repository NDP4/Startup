<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return match ($user->getRoleAttribute()) {
            'admin' => true,
            'customer' => true,
            'crew' => true, // Allow crew to view reviews
            default => false,
        };
    }

    public function view(User $user, Review $review): bool
    {
        return match ($user->getRoleAttribute()) {
            'admin' => true,
            'customer' => $review->booking->customer_id === $user->id,
            'crew' => $review->crew_id === $user->id, // Allow crew to view their own reviews
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return in_array($user->getRoleAttribute(), ['admin']);
    }

    public function update(User $user, Review $review): bool
    {
        return match ($user->getRoleAttribute()) {
            'admin' => true,
            'customer' => $review->booking->customer_id === $user->id,
            default => false,
        };
    }

    public function delete(User $user, Review $review): bool
    {
        return match ($user->getRoleAttribute()) {
            'admin' => true,
            'customer' => $review->booking->customer_id === $user->id,
            default => false,
        };
    }

    public function deleteAny(User $user): bool
    {
        return $user->getRoleAttribute() === 'admin';
    }
}
