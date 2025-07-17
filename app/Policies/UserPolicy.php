<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Просмотр списка пользователей (только админ)
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Редактирование пользователей (только админ)
     */
    public function update(User $user, User $model)
    {
        return $user->isAdmin();
    }

    /**
     * Удаление (админ, но не себя)
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin() && $user->id_user !== $model->id_user;
    }
}