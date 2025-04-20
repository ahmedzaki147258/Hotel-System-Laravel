<?php

namespace App\Helpers;

use App\Http\Resources\CountryResource;
use Lwwcas\LaravelCountries\Models\Country;

class CountryHelper
{
    public static function getVisibleCountries()
    {
        $countries = Country::with('translations')
            ->where('is_visible', 1)
            ->get()
            ->sortBy('translations.0.name')
            ->values();

        return CountryResource::collection($countries);
    }
}
