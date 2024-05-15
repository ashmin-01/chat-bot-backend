<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    /**
     * Get the chat's responses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responses(){
        return $this->hasMany(Response::class);
    }

    /**
     * Get the chat's messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prompts(){
        return $this->hasMany(Prompt::class);
    }

      /**
     * Get the user that owns the chat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

}
