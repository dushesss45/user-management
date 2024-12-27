<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class UserController
 * Контроллер для управления пользователями.
 *
 * Обрабатывает запросы, связанные с CRUD операциями для пользователей.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Сервис для работы с пользователями.
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Конструктор класса.
     *
     * @param UserService $userService Сервис для работы с пользователями.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Получить список пользователей с поддержкой пагинации, сортировки и поиска.
     *
     * @param Request $request Запрос с параметрами: search, sort, perPage.
     * @return JsonResponse JSON-ответ с данными пользователей.
     */
    public function index(Request $request) : JsonResponse
    {
        $users = $this->userService->getAllUsers(
            $request->get('search'),
            $request->get('sort', 'asc'),
            $request->get('perPage', 10)
        );

        return $this->apiResponse(200, 'success', $users);
    }

    /**
     * Получить данные пользователя по ID.
     *
     * @param int $id ID пользователя.
     * @return JsonResponse JSON-ответ с данными пользователя или ошибкой 404.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->findUserById($id);
            return $this->apiResponse(200, 'success', $user);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(404, 'error', $e->getMessage());
        }
    }

    /**
     * Создать нового пользователя.
     *
     * @param Request $request Запрос с данными для создания пользователя.
     * @return JsonResponse JSON-ответ с данными созданного пользователя.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'ip' => 'nullable|ip',
            'comment' => 'nullable|string',
        ]);

        $user = $this->userService->createUser($validated);

        return $this->apiResponse(201, 'success', $user);
    }

    /**
     * Обновить данные существующего пользователя.
     *
     * @param Request $request Запрос с данными для обновления.
     * @param int $id ID пользователя.
     * @return JsonResponse JSON-ответ с обновлёнными данными пользователя или ошибкой 404.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'password' => 'nullable|string|min:8',
            'ip' => 'nullable|ip',
            'comment' => 'nullable|string',
        ]);

        try {
            $user = $this->userService->updateUser($id, $validated);
            return $this->apiResponse(200, 'success', $user);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(404, 'error', $e->getMessage());
        }
    }

    /**
     * Удалить пользователя по ID.
     *
     * @param int $id ID пользователя.
     * @return JsonResponse JSON-ответ с подтверждением удаления или ошибкой 404.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return $this->apiResponse(200, 'success', 'User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(404, 'error', $e->getMessage());
        }
    }
}
