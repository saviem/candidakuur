<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('short')->nullable();
            $table->text('intro');
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('allergy_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allergy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['allergy_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergy_product');
        Schema::dropIfExists('allergies');
    }
};
