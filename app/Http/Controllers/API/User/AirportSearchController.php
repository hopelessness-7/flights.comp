<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\MainController;
use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirportSearchController extends MainController
{
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quest' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), '422');
        }

        $quest = trim($request->quest);

        $airport = Airport::where('city', 'like', "%${quest}%")->orwhere('name', 'like', "%${quest}%")->orWhere('iata', 'like', "%${quest}%")->get();


        if (count($airport) != 0) {
            return $this->sendResponse($airport);
        } else {
            return $this->sendResponse([], '', '204');
        }
    }
}
