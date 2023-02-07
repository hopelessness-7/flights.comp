<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\FlightRequest;
use App\Http\Resources\AirportResource;
use App\Http\Resources\FlightResource;
use App\Models\Airport;
use App\Models\Flight;

class FlightController extends MainController
{
    public function index(FlightRequest $request): \Illuminate\Http\JsonResponse
    {
        $inputData = $request->all();

        $fromAirport = Airport::where('iata', $inputData['from'])->first();
        $toAirport = Airport::where('iata', $inputData['to'])->first();

        $query = Flight::where([
            ['from_id', '=', $fromAirport->id],
            ['to_id', '=', $toAirport->id]
        ]);

        if (isset($inputData['date2'])) {
            $query = Flight::where([
                ['from_id', '=', $fromAirport->id],
                ['to_id', '=', $toAirport->id]
            ])->orWhere(function ($flightQuery) use ($fromAirport, $toAirport) {
                $flightQuery->where([
                    ['from_id', '=', $toAirport->id],
                    ['to_id', '=', $fromAirport->id]
                ]);
            });
        }

        $flights = $query->orderBy('id', 'DESC')->get();

        $data = [];

        foreach ($flights as $flight => $value) {
            if ($value->count_passengers >= $inputData['passengers']) {
                if ($value->from_id == $fromAirport->id) {
                    $data['flight_from'][] = new FlightResource($value);
                } else {
                    $data['flight_back'][] = new FlightResource($value);
                }
            }
        }

        return $this->sendResponse($data);

    }
}
