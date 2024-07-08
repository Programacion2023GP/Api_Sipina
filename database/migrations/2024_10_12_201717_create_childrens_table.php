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
        Schema::create('childrens', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('lastName',100);
            $table->string('secondSurname',100);
            $table->integer('age');
            $table->date('birtDate');
            $table->string('placeWas',100);
            $table->string('Rfc',12);
            $table->string('typeWork',100);
            $table->time('initialSchedule');
            $table->time('finalSchedule');
            $table->string('tutor',150);
            $table->text('conditions');
            $table->text('observations');
            $table->foreignId('users_id')->constrained('users','id');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('childrens');
    }
};
