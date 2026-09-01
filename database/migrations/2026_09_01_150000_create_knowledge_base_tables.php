<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('short')->nullable();
            $table->text('intro');
            $table->string('access')->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('status');
            $table->text('why');
            $table->text('conditions')->nullable();
            $table->text('notes')->nullable();
            $table->json('aliases')->nullable();
            $table->string('access')->default('public');
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary');
            $table->string('access')->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('guide_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->string('heading')->nullable();
            $table->text('body');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('day');
            $table->string('weekday');
            $table->unsignedTinyInteger('week');
            $table->timestamps();
        });

        Schema::create('menu_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_day_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('text');
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_meal_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_meal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('unmatched_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query_key')->unique();
            $table->string('query');
            $table->unsignedInteger('count')->default(1);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->foreignId('converted_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unmatched_queries');
        Schema::dropIfExists('menu_meal_product');
        Schema::dropIfExists('menu_meals');
        Schema::dropIfExists('menu_days');
        Schema::dropIfExists('guide_sections');
        Schema::dropIfExists('guides');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
