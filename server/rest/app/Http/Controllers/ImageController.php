<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function fetchImages(Request $request)
    {
        $userId = $request->user()->id;
        
        $sql = "SELECT name, image_path, id FROM images WHERE user_id = ?";
        $images = DB::select($sql, [$userId]);
    
        return response()->json(["images" => $images]);
    }
    
    public function fetchImageDetails(Request $request, $imageId)
    {
        $sql = "SELECT 
                    images.name as image_name,
                    images.image_path, 
                    equipment.manufacturer,
                    equipment.model,
                    equipment.specification,
                    equipment.attachment, 
                    celestial_objects.name as object_name,
                    celestial_objects.id as object_id
                FROM images
                JOIN equipment ON images.equipment_id = equipment.id
                JOIN celestial_objects ON images.object_id = celestial_objects.id
                WHERE images.id = ?";
    
        $image = DB::selectOne($sql, [$imageId]);
    
        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }
    
        return response()->json(["image" => $image]);
    }

    public function updateImageDetails(Request $request, $imageId){
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'manufacturer' => 'required|string',
            'model' => 'required|string',
            'specification' => 'required|string',
            'attachment' => 'required|string',
            'object' => 'required|integer',
        ]);
    
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }
    
        DB::beginTransaction();
        try {
            // Update image details
            DB::update('UPDATE images SET name = ?, object_id = ? WHERE id = ?', [
                $request->name,
                $request->object,
                $imageId
            ]);
    
            // Update equipment details
            DB::update('UPDATE equipment 
                        SET manufacturer = ?, model = ?, specification = ?, attachment = ?
                        WHERE id = (
                            SELECT equipment_id 
                            FROM images 
                            WHERE id = ?
                        )', [
                $request->manufacturer,
                $request->model,
                $request->specification,
                $request->attachment,
                $imageId
            ]);
    
            DB::commit();
            return response()->json(['message' => 'Image details updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update image details'], 500);
        }
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
