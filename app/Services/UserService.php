<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllUsers($search = null, $sort = 'asc',  $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $query->orderBy('name', $sort);

        return $query->paginate(perPage:$perPage);
    }

    public function findUserById($id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found");
        }

        return $user;
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function updateUser($id, array $data): User
    {
        $user = $this->findUserById($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->refresh();

        return $user;
    }

    public function deleteUser($id): bool
    {
        $user = $this->findUserById($id);
        return $user->delete();
    }
}
