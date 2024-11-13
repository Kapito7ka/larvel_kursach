<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        try {
            $users = User::where('status', 'user')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка отримання користувачів',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'phone_numbers' => 'sometimes|string|max:20', // змінено на phone_numbers
                'age' => 'sometimes|integer|nullable|min:1|max:120',
                'password' => 'sometimes|min:6',
                'password_confirmation' => 'required_with:password|same:password',
            ]);

            $user = User::findOrFail($id);

            $updateData = array_filter([
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone_numbers' => $validated['phone_numbers'] ?? null, // змінено на phone_numbers
                'age' => $validated['age'] ?? null,
            ], function($value) {
                return $value !== null;
            });

            if (isset($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Користувача успішно оновлено',
                'user' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка оновлення користувача',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
