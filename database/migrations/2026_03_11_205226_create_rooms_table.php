<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 100);
            $table->string('name_en', 100);
            $table->tinyInteger('stars');
            $table->string('location_ar', 200);
            $table->string('location_en', 200);
            $table->text('description_ar');
            $table->text('description_en');
            $table->integer('price');
            $table->integer('type_id');
            $table->string('type_name_ar');
            $table->string('type_name_en');
            $table->string('wehda_name_ar', 100);
            $table->string('wehda_name_en', 100);
            $table->decimal('area', 8, 2);
            $table->string('look_ar', 100);
            $table->string('look_en', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
