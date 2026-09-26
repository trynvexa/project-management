<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('project')->nullable();
            $table->text('description')->nullable();
            $table->string('member')->nullable();
            $table->date('deadline')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Todo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
