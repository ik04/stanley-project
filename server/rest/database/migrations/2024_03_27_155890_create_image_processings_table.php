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
        Schema::create('image_processings', function (Blueprint $table) {
            $table->id();
            $table->string("technique");
            $table->string("value");
            $table->foreign("image_id")->references("id")->on("images")->onDelete("cascade");
            $table->unsignedBigInteger("image_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_processings');
    }
};
