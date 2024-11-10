<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:unique:users,email,NULL,id,status,user',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
                'age' => 'nullable|integer|min:16',
                'phone_numbers' => 'nullable|string'
            ], [
                'name.required' => 'Ім\'я обов\'язкове',
                'email.required' => 'Email обов\'язковий',
                'email.email' => 'Невірний формат email',
                'email.unique' => 'Такий email вже зареєстрований для користувача',
                'password.required' => 'Пароль обов\'язковий',
                'password.min' => 'Пароль має бути не менше 8 символів',
                'password_confirmation.same' => 'Паролі не співпадають',
                'age.min' => 'Вік має бути не менше 16 років'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'age' => $validated['age'] ?? null,
                'phone_numbers' => $validated['phone_numbers'] ?? null,
                'status' => 'user'
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Реєстрація успішна'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка реєстрації',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registerAdmin(Request $request)
    {
        try {
            \Log::info('Реєстрація адміністратора', ['request' => $request->all()]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', function ($attribute, $value, $fail) {
                    $exists = User::where('email', $value)
                        ->where('status', 'admin')
                        ->exists();
                    if ($exists) {
                        $fail('Такий email вже зареєстрований для адміністратора');
                    }
                }],
                'password' => 'required|string|min:8|confirmed',
                'age' => 'nullable|integer|min:16',
                'phone_numbers' => 'nullable|string',
            ], [
                'name.required' => 'Ім\'я обов\'язкове',
                'email.required' => 'Email обов\'язковий',
                'email.email' => 'Невірний формат email',
                'password.required' => 'Пароль обов\'язковий',
                'password.min' => 'Пароль має бути не менше 8 символів',
                'password.confirmed' => 'Паролі не співпадають',
                'age.min' => 'Вік має бути не менше 16 років'
            ]);

            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'admin',
                'age' => $validated['age'] ?? null,
                'phone_numbers' => $validated['phone_numbers'] ?? null,
            ]);

            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $admin,
                'token' => $token,
                'message' => 'Адміністратор успішно зареєстрований',
                'is_admin' => true
            ], 201);

        } catch (ValidationException $e) {
            \Log::error('Помилка валідації', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Помилка реєстрації адміністратора', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Помилка підключення до бази даних. Будь ласка, спробуйте пізніше.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database connection error'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])
                        ->where('status', 'user')
                        ->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Невірні облікові дані'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Успішний вхід',
                'is_admin' => false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка входу',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function loginAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $admin = User::where('email', $validated['email'])
                         ->where('status', 'admin')
                         ->first();

            if (!$admin || !Hash::check($validated['password'], $admin->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Невірні облікові дані'],
                ]);
            }

            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $admin,
                'token' => $token,
                'message' => 'Успішний вхід адміністратора',
                'is_admin' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка входу',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function logout(Request $request)
    {
    try {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Ви успішно вийшли']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Помилка при виході',
            'error' => $e->getMessage()
        ], 500);
        }
    }
}
