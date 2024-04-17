<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function fetchImages(Request $request){
        $userId = $request->user()->id;
        $images = Image::select("name","image_path","id")->where("user_id",$userId)->get();
        return response()->json(["images" => $images]);
    }
    public function fetchImageDetails(Request $request,$imageId){
        $image = Image::join('equipment', 'images.equipment_id', '=', 'equipment.id')
            ->join('celestial_objects', 'images.object_id', '=', 'celestial_objects.id')
            ->select('images.name as image_name','images.image_path', 'equipment.manufacturer','equipment.model','equipment.specification','equipment.attachment', 'celestial_objects.name as object_name')
            ->where("images.id",$imageId)
            ->first();
        // $image = Image::join('equipment', 'images.equipment_id', '=', 'equipment.id')
        //     ->join('celestial_objects', 'images.object_id', '=', 'celestial_objects.id')
        //     ->join('users', 'images.user_id', '=', 'users.id')
        //     ->select('images.*', 'equipment.name as equipment_name', 'celestial_objects.name as celestial_object_name', 'users.name as user_name')
        //     ->where("images.id",$imageId)
        //     ->first();

        return response()->json(["image" => $image]);
    }
    public function saveImage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'manufacturer' => 'required|string',
            'model' => 'required|string',
            'specification' => 'required|string',
            'attachment' => 'required|string',
            'image' => 'required|image|mimes:png,jpeg,jpg',
            'object' => 'required|integer',
            'technique' => 'nullable|string',
            'value' => 'nullable|integer'
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->all(), 400);
        }

        $validated = $validation->validated();
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $imageName = 'image' . time() . '_' . uniqid() . '.' . $extension;
        Storage::disk('public')->put("/images/" . $imageName, file_get_contents($image));
        $url = Storage::url("images/" . $imageName);
        $publicPath = $url;

        DB::beginTransaction();
        try {
            // Inserting equipment data
            $sql = "INSERT INTO equipment (manufacturer, model, specification, attachment) VALUES (?, ?, ?, ?)";
            DB::insert($sql, [
                $validated['manufacturer'],
                $validated['model'],
                $validated['specification'],
                $validated['attachment']
            ]);
            $equipmentId = DB::getPdo()->lastInsertId();

            // Inserting image data
            $sql = "INSERT INTO images (name, equipment_id, object_id, user_id, image_path) VALUES (?, ?, ?, ?, ?)";
            DB::insert($sql, [
                $validated['name'],
                $equipmentId,
                $validated['object'],
                $request->user()->id,
                $publicPath
            ]);
            $imageId = DB::getPdo()->lastInsertId();

            // Inserting image processing data if provided
            if (isset($validated['value']) && isset($validated['technique'])) {
                $sql = "INSERT INTO image_processings (image_id, technique, value) VALUES (?, ?, ?)";
                DB::insert($sql, [$imageId, $validated['technique'], $validated['value']]);
            }

            DB::commit();
            return response()->json(['message' => 'Image saved']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to save image'], 500);
        }
    }
}
