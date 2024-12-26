<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers(
            $request->get('search'),
            $request->get('sort', 'asc'),
            $request->get('perPage', 10)
        );

        return $this->apiResponse(200, 'success', $users);
    }

    public function show($id)
    {
        try {
            $user = $this->userService->findUserById($id);
            return $this->apiResponse(200, 'success', $user);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(404, 'error', $e->getMessage());
        }
    }

    public function store(Request $request)
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

    public function update(Request $request, $id)
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

    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            return $this->apiResponse(200, 'success', 'User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(404, 'error', $e->getMessage());
        }
    }
}

