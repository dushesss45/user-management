<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Главная страница приложения.
Route::get('/', function () {
    return view('welcome');
});

/**
 * Ресурсные маршруты для управления пользователями.
 *
 * - GET    /users         -> UserController@index
 * - GET    /users/{id}    -> UserController@show
 * - POST   /users         -> UserController@store
 * - PUT    /users/{id}    -> UserController@update
 * - DELETE /users/{id}    -> UserController@destroy
 */
Route::resource('users', UserController::class)->only([
    'index', 'show', 'store', 'update', 'destroy'
]);

/**
 * Маршрут для перенаправления на документацию API.
 *
 * - GET /api-docs -> Перенаправляет на Swagger UI.
 */
Route::get('/api-docs', function () {
    return redirect('/swagger/index.html');
});