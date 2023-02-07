<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class VerificationController extends MainController
{

    public function verify($id, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return $this->sendResponse([]);
    }

    public function resend(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->sendResponse([]);
    }
}

