<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends TenantModel
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'last_interaction_at',
        'relationship_strength_score',
    ];

    protected function casts(): array
    {
        return [
            'last_interaction_at' => 'date',
            'relationship_strength_score' => 'decimal:2',
        ];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'contact_tag');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
