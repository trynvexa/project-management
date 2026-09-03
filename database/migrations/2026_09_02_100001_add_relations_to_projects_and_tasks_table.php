<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PROJECTS: tambah client_id (relasi ke clients), kolom 'client' lama tetap dibiarkan
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')
                ->constrained('clients')->nullOnDelete();
        });

        // TASKS: tambah project_id (relasi ke projects) & assignee_id (relasi ke users)
        // kolom 'project' dan 'member' lama tetap dibiarkan, tidak dihapus
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')
                ->constrained('projects')->nullOnDelete();

            $table->foreignId('assignee_id')->nullable()->after('project_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropConstrainedForeignId('assignee_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
