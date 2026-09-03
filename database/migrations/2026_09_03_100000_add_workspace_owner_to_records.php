<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertExistingDataCanBeOwnedSafely();

        foreach (['clients', 'projects', 'tasks', 'team_members'] as $table) {
            $this->ensureOwnerColumnAndForeignKey($table);
        }

    }

    public function down(): void
    {
        // Intentionally no destructive rollback for production safety.
    }

    private function assertExistingDataCanBeOwnedSafely(): void
    {
        foreach (['clients', 'projects', 'tasks'] as $table) {
            $hasOwnerColumn = Schema::hasColumn($table, 'user_id');
            $unowned = $hasOwnerColumn
                ? DB::table($table)->whereNull('user_id')->count()
                : DB::table($table)->count();

            if ($unowned > 0) {
                throw new RuntimeException("Workspace ownership migration stopped: {$table} contains {$unowned} record(s) without a verified owner.");
            }
        }

    }

    private function ensureOwnerColumnAndForeignKey(string $table): void
    {
        if (! Schema::hasColumn($table, 'user_id')) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->foreignId('user_id')->nullable()->after('id')->index();
            });
        }

        $foreignKeyName = "{$table}_user_id_foreign";
        $existingForeignKey = collect(Schema::getForeignKeys($table))
            ->first(fn (array $key): bool => in_array('user_id', $key['columns'] ?? [], true));

        // A previous interrupted attempt created clients.user_id with MySQL's
        // invalid-looking name "1". Replace only that FK; no rows are touched.
        if ($existingForeignKey && $existingForeignKey['name'] !== $foreignKeyName) {
            Schema::table($table, function (Blueprint $blueprint) use ($existingForeignKey): void {
                $blueprint->dropForeign($existingForeignKey['name']);
            });
            $existingForeignKey = null;
        }

        if (! $existingForeignKey) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->foreign('user_id', "{$table}_user_id_foreign")
                    ->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
