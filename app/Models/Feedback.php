<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    /**
     * Get the feedback's response.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }
}
