<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

     /**
     * Get the prompt's chat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat(){
        return $this->belongsTo(Chat::class);
    }

       /**
     * Get the prompt's response(s).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responses(){
        return $this->hasMany(Response::class);
    }
    
}
