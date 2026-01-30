<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Countable;
use Illuminate\Http\Request;

class ApplicationController extends Controller {
    public function create(){
        $countries = Country::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        // $districts = District::orderBy('name')->get();
        return view('application', compact('countries', 'provinces'));
    }

    public function getDistricts($provinceId){
        $districts = District::where('province_id', $provinceId)
                                    ->orderBy('name')
                                    ->get();

        return response()->json($districts);
    }
}


