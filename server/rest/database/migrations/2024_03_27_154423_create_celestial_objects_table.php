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
        Schema::create('celestial_objects', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreign("type_id")->references("id")->on("celestial_types")->onDelete("cascade");
            $table->unsignedBigInteger("type_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celestial_objects');
    }
};
