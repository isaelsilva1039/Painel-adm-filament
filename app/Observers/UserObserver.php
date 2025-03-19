<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $this->syncRole($user);
    }

    public function updated(User $user): void
    {
        $this->syncRole($user);
    }

    protected function syncRole(User $user): void
    {
        // Atribui o papel com base no campo type
        $role = match ($user->type) {
            'admin' => 'admin',
            'editor' => 'editor',
            'viewer' => 'viewer',
            default => null,
        };

        if ($role) {
            $user->syncRoles([$role]);
        }
    }
}
