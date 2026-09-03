<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertNoAmbiguousLegacyData();

        if (! Schema::hasTable('workspaces')) {
            Schema::create('workspaces', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedBigInteger('owner_id');
                $table->foreign('owner_id', 'workspaces_owner_id_foreign')->references('id')->on('users')->restrictOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('workspace_members')) {
            Schema::create('workspace_members', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('workspace_id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('workspace_id', 'workspace_members_workspace_id_foreign')->references('id')->on('workspaces')->cascadeOnDelete();
                $table->foreign('user_id', 'workspace_members_user_id_foreign')->references('id')->on('users')->restrictOnDelete();
                $table->string('role', 20)->default('member');
                $table->string('status', 20)->default('active');
                $table->timestamps();
                $table->unique(['workspace_id', 'user_id']);
            });
        }

        foreach (['clients', 'projects', 'tasks', 'team_members'] as $table) {
            if (! Schema::hasColumn($table, 'workspace_id')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                    // Restrict deletion rather than introducing a destructive workspace policy.
                    $blueprint->unsignedBigInteger('workspace_id')->nullable()->after('id')->index();
                    $blueprint->foreign('workspace_id', "{$table}_workspace_id_foreign")
                        ->references('id')->on('workspaces')->restrictOnDelete();
                });
            }
        }

    }

    public function down(): void
    {
        // Intentionally no destructive rollback for production safety.
    }

    private function assertNoAmbiguousLegacyData(): void
    {
        foreach (['clients', 'projects', 'tasks', 'team_members'] as $table) {
            if (DB::table($table)->exists()) {
                throw new RuntimeException("Workspace migration stopped: {$table} contains legacy data. No generic workspace ownership or membership mapping is safe; define and approve an explicit mapping before migration.");
            }
        }
    }
};
