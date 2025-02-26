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
        Schema::create('users_read_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->foreignId('system_id')->constrained('system', 'id');
            $table->foreignId('roles_id')->constrained('roles', 'id');
            $table->foreignId('notifications_id')->constrained('notifications', 'id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_read_notifications');
    }
};
