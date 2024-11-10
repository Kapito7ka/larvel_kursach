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
}
