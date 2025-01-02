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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("count");
            $table->string("province");
            $table->string("district");
            $table->string("subDistrict");
            $table->date("date");
            $table->string("proof");
            $table->text("note")->nullable();
            $table->text("reason")->nullable();
            $table->text("status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
