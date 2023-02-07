<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Airport;
use App\Models\Flight;

class PassengerCount implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $fromAirport = Airport::where('iata', $_REQUEST['from'])->first();
        $toAirport = Airport::where('iata', $_REQUEST['to'])->first();

        $queryFrom = Flight::where([
            ['from_id', '=', $fromAirport->id],
            ['to_id', '=', $toAirport->id]
        ]);


        $flights = $queryFrom->orderBy('count_passengers', 'desc')->get();

        // foreach ($flights as $flight) {
        //     if ($flight->count_passengers < $value) continue;
        //         return true;
        // }


        for ($i=0; $i < count($flights); $i++) {
            if ($flights[$i]['count_passengers'] <= $value) continue;
                return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute error valid.';
    }
}
