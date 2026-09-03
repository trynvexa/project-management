<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspaceOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use BelongsToWorkspaceOwner;
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'client', // kolom lama, tetap dipertahankan biar data lama nggak rusak
        'type',
        'start_date',
        'deadline',
        'status',
        'progress',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
        ];
    }

    public function clientRelation(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Hitung ulang progress berdasarkan task yang statusnya Done.
     * Dipanggil setiap kali task berubah status.
     */
    public function recalculateProgress(): void
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            $this->update(['progress' => 0]);

            return;
        }

        $done = $this->tasks()->whereIn('status', ['Completed', 'Done'])->count();

        $this->update(['progress' => (int) round(($done / $total) * 100)]);
    }
}
