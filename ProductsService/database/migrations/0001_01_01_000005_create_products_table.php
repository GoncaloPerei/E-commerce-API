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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->longText('description')->nullable()->default(null);
            $table->double('price')->default(0);
            $table->string('image')->unique();
            $table->integer('stock');
            $table->foreignId('product_category_id')->constrained('product_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_status_id')->nullable()->default(2)->constrained('product_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('products');
    }
};
