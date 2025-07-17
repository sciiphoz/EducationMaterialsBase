<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Material;

class MaterialPolicy
{
    public function view(User $user, Material $material)
    {
        // Приватные материалы видят только автор и админ
        if ($material->isPrivate) {
            return $user->id_user === $material->id_user || $user->isAdmin();
        }
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Material $material)
    {
        return $user->id_user === $material->id_user || $user->isAdmin();
    }

    public function delete(User $user, Material $material)
    {
        return $user->id_user === $material->id_user || $user->isAdmin();
    }
}