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
        Schema::create('enseigne', function (Blueprint $table) {
            $table->string("code_pers");
            $table->integer("code_ec", 20);
            $table->integer("nbh_heure")->nullable(); 
            $table->date("heure_debut")->nullable();
            $table->date("heure_fin")->nullable();
            $table->string("statut", 50)->nullable();
            $table->foreign("code_pers")->references("code_pers")->on("personnel");
            $table->foreign("code_ec")->references("code_ec")->on("ec");


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseigne');
    }
};
