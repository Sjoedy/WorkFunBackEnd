<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The users that belong to the challenge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_users')->withTimestamps();
    }
}
