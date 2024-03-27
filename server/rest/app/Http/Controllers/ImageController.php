<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Image;
use App\Models\ImageProcessing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function saveImage(Request $request){
        $validation = Validator::make($request->all(),[
            'name' =>'required|string',
            'manufacturer' => 'required|string',
            "model" => "required|string",
            "specification"=> "required|string",
            "attachment" => "required|string",
            "image" => "required|image|mimes:png,jpeg,jpg",
            "object" => "required|integer",
            "technique" =>"nullable|string",
            "value" => "nullable|integer" //replace this garbage
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'image' . time() . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->put("/images/".$imageName, file_get_contents($image));
            $url = Storage::url("images/".$imageName);
            $publicPath = public_path($url);
            $equipment = Equipment::create([
                "manufacturer" => $validated["manufacturer"],
                "model" => $validated["model"],
                "specification" => $validated["specification"],
                "attachment" => $validated["attachment"]
            ]);
            $image = Image::create([
                "name" => $validated["name"],
                "equipment_id" => $equipment->id,
                "object_id" => $validated["object"],
                "user_id" => $request->user()->id,
                "image_path" => $publicPath
            ]);
            if(isset($validated["value"]) && isset($validated["technique"])){
                $proccessing = ImageProcessing::create([
                    "image_id" => $image->id,
                    "technique" => $validated["technique"],
                    "value" => $validated["value"]
                ]);
            }
            return response()->json(["message" => "image saved"]);
    }
}
