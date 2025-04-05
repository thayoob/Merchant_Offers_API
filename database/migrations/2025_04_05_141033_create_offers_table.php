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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('offer_amount', 10, 2);
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->unsignedBigInteger('merchant_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
