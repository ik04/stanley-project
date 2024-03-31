<?php

namespace App\Http\Controllers;

use App\Models\CelestialObject;
use Illuminate\Http\Request;

class CelestialObjectController extends Controller
{
    public function index(){
        return response()->json(["categories" => CelestialObject::all()]);
    }
}
