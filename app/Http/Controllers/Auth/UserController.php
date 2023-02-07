<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Http\Resources\BookingResource;
use App\Http\Resources\FlightResource;
use App\Http\Resources\PassengerResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends MainController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getUserInfo(): \Illuminate\Http\JsonResponse
    {
        return $this->sendResponse(new UserResource(User::find(Auth::user()->id)));
    }

    public function getUserBookingInfo()
    {
        $passenger = Passenger::where('user_data_id', Auth::user()->user_data_id)->first();
        if (isset($passenger->booking_id)) {
            $booking = Booking::where('id', $passenger->booking_id)->get();
            return $this->sendResponse(BookingResource::collection($booking));
        } else {
            return $this->sendResponse('Пользователь не имеет бронирования');
        }
    }

    public function logout()
    {
        Auth::logout();

        return $this->sendResponse([]);
    }

    public function refresh()
    {
        $data = [
            'user' => new UserResource(User::find(Auth::user()->id)),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ];

        return $this->sendResponse($data);
    }

}
