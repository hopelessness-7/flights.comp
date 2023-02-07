<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MainController;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends MainController
{
    public function register(Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        $validators = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' =>'required',
            'phone' => 'required|string|unique:users',
            'email' => 'required|email|string|unique:users',
            'document_number' => 'required|string|max:10|min:10|unique:user_data',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->errors(), 422);
        }

        DB::beginTransaction();

        try {

            $userData = UserData::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'document_number' => $request->document_number,
                'birth_date' => $request->birth_date,
            ]);

            User::create([
                'user_data_id' => $userData->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

        } catch (\Throwable $th) {

            DB::rollback();

            return $this->sendError('Ошибка, сервер не смог обработать запрос, попробуйте позже','500');
        }

        DB::commit();

        return $this->sendResponse([]);
    }
}
