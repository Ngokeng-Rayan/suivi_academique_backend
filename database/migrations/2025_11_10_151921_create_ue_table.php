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
        Schema::create('ue', function (Blueprint $table) {
            $table->string("code_ue")->primary();
            $table->string("label_ue",256);
            $table->text("desc_ue",256);
            $table->integer("code_niveau");
            $table->foreign("code_niveau")->references("code_niveau")->on("niveau");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ue');
    }
};
