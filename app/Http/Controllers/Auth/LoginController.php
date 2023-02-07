<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MainController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends MainController
{
    public function login(Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        // Валидатор
        $validators = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|string|min:6'
        ]);
        // Проверка валидатора
        if ($validators->fails()) {
            return $this->sendError($validators->errors(), '422');
        }
        // Получение данных с формы
        $credentials = $request->only('phone', 'password');
        // Проверка пользователя по данным с формы
        $token = Auth::attempt($credentials);
        // Если пользователь не найден, то выкидываем исключение
        if (!$token) {
            return $this->sendError('Unauthorized', 401);
        }
        $data = [
            'user_data' => new UserResource(User::find(Auth::user()->id)),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ];

        return $this->sendResponse($data);
    }
}
