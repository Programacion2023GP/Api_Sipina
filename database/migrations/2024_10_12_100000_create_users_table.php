<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->integer('numberNomina');
            $table->string('name');
            $table->string('lastName');
            $table->string('secondSurname');
            $table->integer('typeUser');


            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->foreignId('institution_id')->nullable()->constrained('institutions', 'id');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
