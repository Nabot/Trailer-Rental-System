<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('quotes.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quote $quote): bool
    {
        return $user->can('quotes.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('quotes.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quote $quote): bool
    {
        return $user->can('quotes.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quote $quote): bool
    {
        return $user->can('quotes.delete');
    }
}
