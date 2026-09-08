<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_guides', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->json('tags')->nullable();
            $table->text('summary');
            $table->text('body_common')->nullable();
            $table->text('body_practical')->nullable();
            $table->text('body_contact')->nullable();
            $table->boolean('published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_guides');
    }
};
