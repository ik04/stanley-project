<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreign("equipment_id")->references("id")->on("equipment")->onDelete("cascade");
            $table->unsignedBigInteger("equipment_id");
            $table->foreign("object_id")->references("id")->on("celestial_objects")->onDelete("cascade");
            $table->unsignedBigInteger("object_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->unsignedBigInteger("user_id");
            $table->string("image_path");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
