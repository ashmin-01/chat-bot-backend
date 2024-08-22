<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id' , 'prompt_content'
    ];

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

    public function toArray()
    {
        $array = parent::toArray();

        // Ensure the prompt_content is handled correctly
        $array['prompt_content'] = $this->prompt_content;

        return $array;
    }

}
