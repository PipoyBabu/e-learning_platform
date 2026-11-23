<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BusinessLocationController extends Controller
{
    public function show(): View
    {
        return view('business-location.show', [
            'location' => config('business.location'),
        ]);
    }
}
