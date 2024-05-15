<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id' , 'feedback_type' , 'context' , 'regenerate_review'
    ]; 

    /**
     * Get the feedback's response.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }
}
