<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  Lwwcas\LaravelCountries\Models\Country;

class TestController extends Controller
{
    public function test(){
        $country1 = Country::whereIso('BR')->first();
        $country2 = Country::whereIsoAlpha3('BRA')->first();
        $country3 = Country::whereSlug('brasil')->first();

        return response()->json([
            'country1' => $country1,
            'country2' => $country2,
            'country3' => $country3,
        ]);
    }
}
