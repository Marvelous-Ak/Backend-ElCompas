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
            $table->string('brand',100);
            $table->string('name',100);
            $table->string('image',255);
            $table->boolean('promo');
            $table->decimal('price', 10, 2);// 10 dígitos en total, 2 decimales
            $table->decimal('priceAnt', 10, 2);
            $table->text('description');
            $table->integer('stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
