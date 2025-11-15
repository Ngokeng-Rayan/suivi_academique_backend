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
        Schema::create('programmation', function (Blueprint $table) {
            $table->string('code_ec',50);
            $table->string('num_salle',50);
            $table->string('code_pers',50);
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->integer('nbre_heure');
            $table->string('status');
            $table->timestamps();
            $table->primary(['code_ec','num_salle','code_pers']);
            $table->foreign('code_ec')->references('code_ec')->on ('ec')->onDelete('cascade');
            $table->foreign('num_salle')->references('num_sale')->on ('salle')->onDelete('cascade');
            $table->foreign('code_pers')->references('code_pers')->on ('personnel')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmation');
    }
};
