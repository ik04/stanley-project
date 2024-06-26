<?php

use App\Http\Controllers\CelestialObjectController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("register",[UserController::class,"register"]);
Route::post("login",[UserController::class,"login"]);
Route::get("user-data",[UserController::class,"userData"]);
Route::get("categories",[CelestialObjectController::class,"index"]);

Route::middleware(["auth:sanctum"])->group(function(){
    Route::get("image-details/{imageId}",[ImageController::class,"fetchImageDetails"]);
    Route::patch("update-details/{imageId}",[ImageController::class,"updateImageDetails"]);
    Route::post("logout",[UserController::class,"logout"]);
    Route::post("upload",[ImageController::class,"saveImage"]);
    Route::get("fetch-images",[ImageController::class,"fetchImages"]);
    Route::post("isLog",function(){
        return response()->noContent();
    });
});
