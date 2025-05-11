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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('deal_title');
            $table->string('description');
            $table->string('deal_category');
            $table->string('deal_type');
            $table->string('business_name');
            $table->string('location');
            $table->string('uses');
            $table->double('total_saving');
            $table->date('start_date');
            $table->date('expired_date');
            $table->string('deal_image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
