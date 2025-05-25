<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aspirasi extends Model
{
    use HasFactory;
    protected $table = 'aspirasi';

    protected $fillable = [
        'nama',
        'jenis_aspirasi',
        'description',
    ];

    /**
     * Relasi ke model User berdasarkan nama.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nama', 'name');
    }
}
