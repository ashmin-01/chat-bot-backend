<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

     /**
     * Get the chat that the response is a part of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat(){
        return $this->belongsTo(Chat::class);
    }

     /**
     * Get the response's prompt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prompt(){
        return $this->belongsTo(Prompt::class);
    }

     /**
     * Get the feedback associated with the response.
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }
}
