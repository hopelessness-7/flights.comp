<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BookingController extends MainController
{
    public function store(BookingRequest $request): \Illuminate\Http\JsonResponse
    {
        $inputData = $request->all();

        DB::beginTransaction();

        try {

            $booking = new Booking;

            $booking->flight_from = $inputData['flight_from']['id'];
            $booking->date_from = $inputData['flight_from']['date'];

            $flight_from = Flight::find($booking->flight_from);
            $flight_from->count_passengers = $flight_from->count_passengers - count($inputData['passengers']);
            if ($flight_from->count_passengers == 0) {
                $flight_from->delete();
            } else {
                $flight_from->save();
            }
            if(isset($inputData['flight_back'])) {
                $booking->flight_back = $inputData['flight_back']['id'];
                $booking->date_back = $inputData['flight_back']['date'];

                $flight_back = Flight::find($booking->flight_back);
                $flight_back->count_passengers = $flight_back->count_passengers - count($inputData['passengers']);
                if ($flight_back->count_passengers == 0) {
                    $flight_back->delete();
                } else {
                    $flight_back->save();
                }
            }

            /**
             * сгенерировать КОД рейса, которого нет в базе данных
             * генерировать до тех пор, пока код не станет уникальным
             */
            $code = '';
            while (true) {
                $code = strtoupper(\Str::random(5));
                if (!Booking::where('code', $code)->first()) {
                    break;
                }
            }

            $booking->code = $code;

            $booking->save();

            foreach ($inputData['passengers'] as $p) {
                $userDoc = UserData::where('document_number', $p['document_number'])->first();
                if (isset($userDoc->document_number)) {
                    Passenger::create([
                        'booking_id' => $booking->id,
                        'user_data_id' => $userDoc->id
                    ]);
                } else {
                    $UserData = new UserData;
                    $UserData->first_name = $p['first_name'];
                    $UserData->last_name = $p['last_name'];
                    $UserData->birth_date = $p['birth_date'];
                    $UserData->document_number = $p['document_number'];
                    $UserData->save();

                    Passenger::create([
                        'booking_id' => $booking->id,
                        'user_data_id' => $UserData->id
                    ]);
                }
            }

        } catch (Exception $e) {

            /**
             * Откат транзакции, если произошла ошибка
             */
            DB::rollback();

            return $this->sendError('Ошибка добавления данных, попробуйте позже','500');
        }

        DB::commit();



        return $this->sendResponse($booking->code);
    }

    public function showBooking($code): \Illuminate\Http\JsonResponse
    {
        $booking = Booking::with('flight_from_to_booking', 'flight_back_to_booking', 'passengers')->where('code', $code)->first();

        if (!$booking){
            return $this->sendError("Бронирование с кодом = $code не найден",'404');
        } else {
            return $this->sendResponse(new BookingResource($booking));
        }
    }

    public function chooseSeat($code, Request $request): \Illuminate\Http\JsonResponse
    {
        $booking = Booking::with('flight_from_to_booking', 'flight_back_to_booking', 'passengers')->where('code', $code)->first();

        if (!$booking){
            return $this->sendError("Бронирование с кодом = $code не найден",'404');
        }

        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'passenger' => 'required|integer|exists:passengers,id',
            'seat' => 'required|string|min:2|max:2',
            'type' => 'required|in:from,back'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 422);
        }

        if($inputData['type'] == 'back') {
            if (!$booking->flight_back) {
                return $this->sendError("Бронирование $code не имеет обратного рейса", '422');
            }
        }

        $passenger = $booking->passengers->find($inputData['passenger']);
        if (!$passenger) {
            return $this->sendError("Пассажир не входит в это бронирование $code", '403');
        }

        $placeFromOrBack = $inputData['type'] == 'back' ? 'place_back' : 'place_from';

        if($inputData['type'] == 'from') {
            /**
             * получить все Бронирования на один рейс
             */
            $allFromFlightsBookings = Flight::find($booking->flight_from)->from_booking()->with('passengers')->get();

            foreach ($allFromFlightsBookings as $b) {
                /**
                 * собрать всех пассажиров на один рейс и проверьте их места
                 */
                foreach ($b->passengers as $p) {
                    if ($p->{$placeFromOrBack} == strtoupper($inputData['seat'])) {
                        $this->sendError('Место занято', '422');
                    }
                }
            }
        }

        if($inputData['type'] == 'back') {
            /**
             * get all bookings for one flight_back
             */
            $allBackFlightsBookings = Flight::find($booking->flight_back)->back_booking()->with('passengers')->get();

            foreach ($allBackFlightsBookings as $b) {
                /**
                 * собрать всех пассажиров на один обратный рейс и проверьте их места
                 */
                foreach ($b->passengers as $p) {
                    if ($p->{$placeFromOrBack} == strtoupper($inputData['seat'])) {
                        $this->sendError('Место занято', '422');
                    }
                }
            }
        }
        $passenger->{$placeFromOrBack} = strtoupper($inputData['seat']);
        $passenger->save();

        return $this->sendResponse(new BookingResource($booking));
    }

    public function softDeleteBooking($code)
    {
        $booking = Booking::with('flight_from_to_booking', 'flight_back_to_booking', 'passengers')->where('code', $code)->first();

        if (!$booking){
            return $this->sendError("Бронирование с кодом = $code не найден",'404');
        }

        $passengers = Passenger::where('user_data_id', Auth::user()->user_data->id)->get();

        if (!empty($passengers)) {
            foreach ($passengers as $passenger) {
                if ($passenger->booking_id == $booking->id) {

                    $flight_from = Flight::find($booking->flight_from);

                    if ($flight_from == null) {
                        $flight_from = Flight::withTrashed()->find($booking->flight_from);
                        $flight_from->restore();
                        $passenger->delete();
                    } else {
                        $flight_from->count_passengers = $flight_from->count_passengers + $passengers->count();
                    }

                    $booking->delete();

                    return $this->sendResponse([]);
                }
            }
        } else {
            return $this->sendError("Пассажир не входит в бронирование",'403');
        }

//        if ($flight_from->) {
//            $flight_from->delete();
//        } else {
//            $flight_from->save();
//        }

        dd();
    }
}
