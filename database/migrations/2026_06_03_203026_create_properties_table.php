<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('image_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->boolean('has_photo')->default(false);
            $table->unsignedTinyInteger('rooms');
            $table->smallInteger('floor');
            $table->decimal('area', 8, 2);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('title');
            $table->index('has_photo');
            $table->index('rooms');
            $table->index('area');
            $table->index(['has_photo', 'rooms', 'area']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
