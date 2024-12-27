<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UserControllerTest
 *
 * Тесты для проверки функциональности маршрутов UserController.
 *
 * Этот класс содержит тесты для всех маршрутов, определенных в UserController:
 * - Получение списка пользователей.
 * - Получение данных пользователя по ID.
 * - Создание нового пользователя.
 * - Обновление данных пользователя.
 * - Удаление пользователя.
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест получения списка пользователей.
     *
     * Проверяет, что маршрут возвращает статус 200
     * и корректную структуру JSON-ответа с пагинацией.
     *
     * @return void
     */
    public function test_index_route_returns_users()
    {
        User::factory()->count(15)->create();

        $response = $this->getJson('/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'ip',
                            'comment',
                            'created_at',
                            'updated_at',
                        ],
                    ]
                ],
                'message',
            ]);
    }

    /**
     * Тест получения данных пользователя по ID.
     *
     * Проверяет, что маршрут возвращает корректные данные
     * для существующего пользователя.
     *
     * @return void
     */
    public function test_show_route_returns_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'ip' => $user->ip,
                    'comment' => $user->comment,
                ],
                'message' => null,
            ]);
    }

    /**
     * Тест получения данных несуществующего пользователя.
     *
     * Проверяет, что маршрут возвращает статус 404 и сообщение об ошибке.
     *
     * @return void
     */
    public function test_show_route_returns_404_for_nonexistent_user()
    {
        $response = $this->getJson('/users/999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'User not found',
            ]);
    }

    /**
     * Тест создания нового пользователя.
     *
     * Проверяет, что маршрут создает пользователя,
     * возвращает статус 201 и корректные данные.
     *
     * @return void
     */
    public function test_store_route_creates_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'ip' => '127.0.0.1',
            'comment' => 'Test comment',
        ];

        $response = $this->postJson('/users', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'ip' => '127.0.0.1',
                    'comment' => 'Test comment',
                ],
                'message' => null,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    /**
     * Тест обновления данных пользователя.
     *
     * Проверяет, что маршрут обновляет данные существующего пользователя
     * и возвращает корректный ответ.
     *
     * @return void
     */
    public function test_update_route_updates_user()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'comment' => 'Updated comment',
        ];

        $response = $this->putJson("/users/{$user->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => 'Updated Name',
                    'email' => $user->email,
                    'ip' => $user->ip,
                    'comment' => 'Updated comment',
                ],
                'message' => null,
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'comment' => 'Updated comment',
        ]);
    }

    /**
     * Тест удаления пользователя.
     *
     * Проверяет, что маршрут удаляет пользователя
     * и возвращает статус 200 с подтверждающим сообщением.
     *
     * @return void
     */
    public function test_destroy_route_deletes_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => 'User deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Тест удаления несуществующего пользователя.
     *
     * Проверяет, что маршрут возвращает статус 404 и сообщение об ошибке.
     *
     * @return void
     */
    public function test_destroy_route_returns_404_for_nonexistent_user()
    {
        $response = $this->deleteJson('/users/999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'User not found',
            ]);
    }
}
