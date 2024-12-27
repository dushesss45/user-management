<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * Сервис для работы с пользователями.
 *
 * Обрабатывает операции CRUD для модели User, включая пагинацию, поиск и обновление данных.
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Получить список всех пользователей с поддержкой пагинации, сортировки и поиска.
     *
     * @param string|null $search Поисковая строка для фильтрации по имени.
     * @param string $sort Направление сортировки ('asc' или 'desc').
     * @param int $perPage Количество пользователей на страницу.
     * @return LengthAwarePaginator Пагинированный список пользователей.
     */
    public function getAllUsers(string $search = null, string $sort = 'asc', int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $query->orderBy('name', $sort);

        return $query->paginate(perPage: $perPage);
    }

    /**
     * Найти пользователя по ID.
     *
     * @param int $id ID пользователя.
     * @return User Найденный пользователь.
     * @throws ModelNotFoundException Если пользователь не найден.
     */
    public function findUserById(int $id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found");
        }

        return $user;
    }

    /**
     * Создать нового пользователя.
     *
     * @param array $data Данные для создания пользователя (name, email, password, ip, comment).
     * @return User Созданный пользователь.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    /**
     * Обновить данные существующего пользователя.
     *
     * @param int $id ID пользователя.
     * @param array $data Обновлённые данные (name, password, ip, comment).
     * @return User Обновлённый пользователь.
     * @throws ModelNotFoundException Если пользователь не найден.
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->findUserById($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->refresh();

        return $user;
    }

    /**
     * Удалить пользователя по ID.
     *
     * @param int $id ID пользователя.
     * @return bool Статус удаления (true - успешно удалён).
     * @throws ModelNotFoundException Если пользователь не найден.
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->findUserById($id);
        return $user->delete();
    }
}
