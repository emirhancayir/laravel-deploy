<?php

namespace App\Policies;

use App\Models\Konusma;
use App\Models\User;

class KonusmaPolicy
{
    /**
     * Konusmayi goruntuleyebilir mi?
     */
    public function view(User $user, Konusma $konusma): bool
    {
        return $user->id === $konusma->alici_id || $user->id === $konusma->satici_id;
    }

    /**
     * Konusmayi guncelleyebilir mi?
     */
    public function update(User $user, Konusma $konusma): bool
    {
        return $user->id === $konusma->alici_id || $user->id === $konusma->satici_id;
    }

    /**
     * Konusmayi silebilir mi?
     */
    public function delete(User $user, Konusma $konusma): bool
    {
        return $user->id === $konusma->alici_id || $user->id === $konusma->satici_id;
    }
}
